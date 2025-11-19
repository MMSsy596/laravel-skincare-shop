<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    // Địa chỉ shop mặc định: Hồ Tùng Mậu, Cầu Giấy, Hà Nội
    const SHOP_ADDRESS = 'Hồ Tùng Mậu, Cầu Giấy, Hà Nội';
    const SHOP_LAT = 21.0285; // Latitude của Hồ Tùng Mậu, Cầu Giấy
    const SHOP_LNG = 105.8048; // Longitude của Hồ Tùng Mậu, Cầu Giấy
    const SHIPPING_RATE_PER_KM = 3000; // 3k/km

    /**
     * Tính khoảng cách và phí vận chuyển
     */
    public function calculateShipping(Request $request)
    {
        try {
            // Get raw values to check if coordinates are provided
            $hasLat = !empty($request->latitude);
            $hasLng = !empty($request->longitude);
            $hasCoordinates = $hasLat && $hasLng;
            
            $validator = Validator::make($request->all(), [
                'address' => $hasCoordinates ? 'nullable|string' : 'required|string|min:3',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Địa chỉ không hợp lệ.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $validated = $validator->validated();

            $address = !empty($validated['address']) ? trim($validated['address']) : null;
            $lat = $validated['latitude'] ?? null;
            $lng = $validated['longitude'] ?? null;

        // Nếu có tọa độ, tính khoảng cách trực tiếp
        if ($lat && $lng) {
            $distance = $this->calculateDistance($lat, $lng, self::SHOP_LAT, self::SHOP_LNG);
        } elseif ($address) {
            // Nếu không có tọa độ nhưng có địa chỉ, geocode địa chỉ
            $geocode = $this->geocodeAddress($address);
            if ($geocode) {
                $lat = $geocode['lat'];
                $lng = $geocode['lng'];
                $distance = $this->calculateDistance($lat, $lng, self::SHOP_LAT, self::SHOP_LNG);
            } else {
                // Nếu không geocode được, sử dụng khoảng cách mặc định
                $distance = null;
            }
        } else {
            // Không có cả tọa độ lẫn địa chỉ (không nên xảy ra do validation)
            $distance = null;
        }

        if ($distance !== null) {
            $shippingFee = round($distance * self::SHIPPING_RATE_PER_KM);
            // Phí tối thiểu 10,000 VNĐ
            $shippingFee = max($shippingFee, 10000);
        } else {
            // Nếu không tính được khoảng cách, sử dụng phí mặc định
            $distance = null;
            $shippingFee = 30000;
        }

        return response()->json([
            'success' => true,
            'distance' => $distance ? round($distance, 2) : null,
            'shipping_fee' => $shippingFee,
            'latitude' => $lat,
            'longitude' => $lng,
            'shop_address' => self::SHOP_ADDRESS,
        ]);
        } catch (\Exception $e) {
            \Log::error('Shipping calculation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Không thể tính phí vận chuyển. Vui lòng thử lại sau.',
            ], 500);
        }
    }

    /**
     * Geocode địa chỉ thành tọa độ (sử dụng Google Geocoding API)
     */
    private function geocodeAddress($address)
    {
        $apiKey = config('services.google.maps_api_key');
        
        if (!$apiKey) {
            // Nếu không có API key, thử sử dụng OpenStreetMap Nominatim (miễn phí)
            return $this->geocodeWithNominatim($address);
        }

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address . ', Hà Nội, Việt Nam',
                'key' => $apiKey,
                'language' => 'vi',
            ]);

            $data = $response->json();

            if ($data['status'] === 'OK' && !empty($data['results'])) {
                $location = $data['results'][0]['geometry']['location'];
                return [
                    'lat' => $location['lat'],
                    'lng' => $location['lng'],
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Geocoding error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Geocode sử dụng OpenStreetMap Nominatim (miễn phí, không cần API key)
     */
    private function geocodeWithNominatim($address)
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => config('app.name'),
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $address . ', Hà Nội, Việt Nam',
                'format' => 'json',
                'limit' => 1,
            ]);

            $data = $response->json();

            if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                return [
                    'lat' => (float) $data[0]['lat'],
                    'lng' => (float) $data[0]['lon'],
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Nominatim geocoding error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Tính khoảng cách giữa 2 điểm (Haversine formula)
     * Trả về khoảng cách tính bằng km
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Bán kính Trái Đất tính bằng km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
