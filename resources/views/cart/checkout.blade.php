@extends('layouts.app')

@section('content')
<div class="checkout-page">
<!-- Google Maps API -->
@if(config('services.google.maps_api_key'))
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&language=vi&region=VN" async defer></script>
@endif

<div class="container py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <h2 class="mb-0">
                <i class="fas fa-credit-card me-2 text-primary"></i>Thanh toán đơn hàng
            </h2>
            <p class="text-muted">Vui lòng kiểm tra thông tin đơn hàng và điền thông tin giao hàng</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-shipping-fast me-2"></i>Thông tin giao hàng
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cart.checkout.submit') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>Địa chỉ giao hàng
                            </label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="shipping_address" name="shipping_address" 
                                       placeholder="Nhập địa chỉ hoặc chọn trên bản đồ..." required
                                       autocomplete="off">
                                <button type="button" class="btn btn-outline-primary" id="getLocationBtn">
                                    <i class="fas fa-crosshairs me-1"></i>Lấy vị trí
                                </button>
                            </div>
                            <textarea class="form-control" id="shipping_address_detail" name="shipping_address_detail" rows="2" 
                                      placeholder="Số nhà, tên đường, phường/xã (nếu cần bổ sung)..." 
                                      style="display: none;"></textarea>
                            <div id="addressInfo" class="mt-3 p-3 bg-light rounded border" style="display: none;">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-map-marker-alt text-primary me-2 mt-1"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold mb-2 text-dark">
                                            <i class="fas fa-location-dot me-1 text-primary"></i>Thông tin vị trí
                                        </div>
                                        <div id="locationDetails" class="small text-muted">
                                            <div id="fullAddress" class="mb-2"></div>
                                            <div id="coordinates" class="mb-2"></div>
                                            <div id="distanceInfo" class="text-primary fw-semibold"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="latitude" name="latitude">
                            <input type="hidden" id="longitude" name="longitude">
                            <input type="hidden" id="distance" name="distance">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone me-1"></i>Số điện thoại
                            </label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   placeholder="Nhập số điện thoại liên hệ..." required>
                        </div>
                        
                        <!-- Voucher Code -->
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-ticket-alt me-1"></i>Mã giảm giá
                            </label>
                            
                            @if(isset($publicVouchers) && $publicVouchers->count() > 0)
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-2 d-block">
                                        <i class="fas fa-gift me-1 text-primary"></i>Voucher có sẵn cho bạn:
                                    </label>
                                    <div class="row g-2" id="publicVouchersList">
                                        @foreach($publicVouchers as $publicVoucher)
                                            @php
                                                $discountAmount = $publicVoucher->calculateDiscount($subtotal);
                                            @endphp
                                            <div class="col-md-6">
                                                <div class="card voucher-card border h-100" 
                                                     data-voucher-code="{{ $publicVoucher->code }}"
                                                     data-voucher-discount="{{ $discountAmount }}"
                                                     style="cursor: pointer; transition: all 0.3s;"
                                                     id="voucher-card-{{ $publicVoucher->code }}">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div class="flex-grow-1">
                                                                <div class="d-flex align-items-center mb-2">
                                                                    <i class="fas fa-ticket-alt text-primary me-2"></i>
                                                                    <strong class="text-primary">{{ $publicVoucher->code }}</strong>
                                                                </div>
                                                                @if($publicVoucher->name)
                                                                    <p class="mb-1 small text-muted">{{ $publicVoucher->name }}</p>
                                                                @endif
                                                                @if($publicVoucher->description)
                                                                    <p class="mb-0 small">{{ Str::limit($publicVoucher->description, 40) }}</p>
                                                                @endif
                                                            </div>
                                                            <div class="text-end">
                                                                @if($publicVoucher->type === 'fixed')
                                                                    <span class="badge bg-success fs-6">-{{ number_format($publicVoucher->value) }} VNĐ</span>
                                                                @else
                                                                    <span class="badge bg-warning text-dark fs-6">-{{ $publicVoucher->value }}%</span>
                                                                    @if($publicVoucher->max_discount)
                                                                        <br><small class="text-muted">Tối đa {{ number_format($publicVoucher->max_discount) }} VNĐ</small>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            <label class="form-label small text-muted mb-2 d-block">
                                <i class="fas fa-keyboard me-1"></i>Hoặc nhập mã voucher:
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="voucher_code" name="voucher_code" 
                                       placeholder="Nhập mã voucher..." 
                                       value="{{ $voucher->code ?? old('voucher_code') }}">
                                <button type="button" class="btn btn-primary" id="applyVoucherBtn" onclick="applyVoucher()">
                                    <i class="fas fa-check me-1"></i>Áp dụng
                                </button>
                                @if($voucher)
                                <button type="button" class="btn btn-outline-danger" id="removeVoucherBtn" onclick="removeVoucher()">
                                    <i class="fas fa-times"></i>
                                </button>
                                @endif
                            </div>
                            <div id="voucherMessage" class="mt-2">
                                @if($voucher)
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>{{ $voucher->code }}</strong>
                                    @if($voucher->name)
                                        - {{ $voucher->name }}
                                    @endif
                                    <br>
                                    <small>Giảm: {{ number_format($voucherDiscount) }} VNĐ</small>
                                </div>
                                @endif
                            </div>
                            <input type="hidden" id="voucher_discount" name="voucher_discount" value="{{ $voucherDiscount ?? 0 }}">
                        </div>
                        
                        <!-- Payment Method Selection -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-credit-card me-1"></i>Phương thức thanh toán
                            </label>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-check payment-method-card">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="payment_cash" value="cash" checked>
                                        <label class="form-check-label" for="payment_cash">
                                            <div class="payment-method-content">
                                                <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                                <h6>Tiền mặt</h6>
                                                <small class="text-muted">Thanh toán khi nhận hàng</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check payment-method-card">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="payment_bank" value="bank_transfer">
                                        <label class="form-check-label" for="payment_bank">
                                            <div class="payment-method-content">
                                                <i class="fas fa-university fa-2x text-primary mb-2"></i>
                                                <h6>Chuyển khoản</h6>
                                                <small class="text-muted">Ngân hàng Vietcombank</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check payment-method-card">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="payment_qr" value="qr_code">
                                        <label class="form-check-label" for="payment_qr">
                                            <div class="payment-method-content">
                                                <i class="fas fa-qrcode fa-2x text-warning mb-2"></i>
                                                <h6>Quét mã QR</h6>
                                                <small class="text-muted">Thanh toán nhanh</small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>Lưu ý quan trọng
                            </h6>
                            <ul class="mb-0">
                                <li>Đơn hàng sẽ được xử lý trong vòng 24 giờ</li>
                                <li>Thời gian giao hàng: 2-5 ngày làm việc</li>
                                <li>Thanh toán khi nhận hàng (COD)</li>
                                <li>Kiểm tra hàng trước khi thanh toán</li>
                            </ul>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-check-circle me-2"></i>Xác nhận đặt hàng
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-bag me-2"></i>Đơn hàng của bạn
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush mb-3">
                        @php $hasStockIssues = false; @endphp
                        @foreach($cart as $id => $item)
                            @php 
                                $itemSubtotal = $item['price'] * $item['quantity'];
                                $stock = $item['stock'] ?? 0;
                                $isOutOfStock = $stock < $item['quantity'];
                                if ($isOutOfStock) $hasStockIssues = true;
                            @endphp
                            <li class="list-group-item d-flex justify-content-between align-items-start {{ $isOutOfStock ? 'border-warning' : '' }}">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : 'https://via.placeholder.com/50x50/CCCCCC/FFFFFF?text=Product' }}" 
                                         style="width:50px; height:50px; object-fit:cover;" 
                                         class="rounded me-3">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $item['name'] }}</h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Số lượng: {{ $item['quantity'] }}</span>
                                            <span class="text-primary fw-bold">{{ number_format($item['price']) }} VNĐ</span>
                                        </div>
                                        @if($isOutOfStock)
                                            <div class="mt-1">
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Chỉ còn {{ $stock }} sản phẩm
                                                </span>
                                            </div>
                                        @elseif($stock <= 5)
                                            <div class="mt-1">
                                                <span class="badge bg-info">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Còn {{ $stock }} sản phẩm
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    <strong>{{ number_format($itemSubtotal) }} VNĐ</strong>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    
                    @if($hasStockIssues)
                        <div class="alert alert-warning">
                            <h6 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Cảnh báo về kho hàng
                            </h6>
                            <p class="mb-0">
                                Một số sản phẩm vượt quá số lượng có sẵn. 
                                Vui lòng quay lại giỏ hàng để điều chỉnh.
                            </p>
                        </div>
                    @endif
                    
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Tạm tính:</span>
                                <span id="subtotal">{{ number_format($subtotal) }} VNĐ</span>
                            </div>
                            @if($voucher && $voucherDiscount > 0)
                            <div class="d-flex justify-content-between align-items-center mb-2" id="voucherDiscountRow">
                                <span>Giảm giá voucher:</span>
                                <span class="text-success" id="voucherDiscount">-{{ number_format($voucherDiscount) }} VNĐ</span>
                            </div>
                            @else
                            <div class="d-flex justify-content-between align-items-center mb-2" id="voucherDiscountRow" style="display: none;">
                                <span>Giảm giá voucher:</span>
                                <span class="text-success" id="voucherDiscount">0 VNĐ</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Phí vận chuyển:</span>
                                <span id="shippingFeeDisplay">
                                    @if(isset($shippingFee) && $shippingFee > 0)
                                        {{ number_format($shippingFee) }} VNĐ
                                    @else
                                        <span class="text-success">Miễn phí</span>
                                    @endif
                                </span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Tổng cộng:</strong>
                                <strong class="text-primary fs-5" id="finalTotal">{{ number_format($subtotal - $voucherDiscount + $shippingFee) }} VNĐ</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-shield-alt me-2"></i>Bảo mật & Hỗ trợ
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <i class="fas fa-lock fa-2x text-success mb-2"></i>
                            <p class="small mb-0">Thanh toán an toàn</p>
                        </div>
                        <div class="col-6">
                            <i class="fas fa-headset fa-2x text-primary mb-2"></i>
                            <p class="small mb-0">Hỗ trợ 24/7</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.voucher-card {
    transition: all 0.3s ease;
    border: 2px solid #dee2e6 !important;
}

.voucher-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
    border-color: #0d6efd !important;
}

.voucher-card.border-primary {
    border-color: #0d6efd !important;
    background-color: #f0f7ff !important;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2) !important;
}
</style>

<script>
// Ensure functions are available globally
const subtotal = {{ $subtotal }};
let voucherDiscount = {{ $voucherDiscount ?? 0 }};
let shippingFee = {{ $shippingFee ?? 0 }};

// Shop location (Hồ Tùng Mậu, Cầu Giấy, Hà Nội)
const SHOP_LAT = 21.0285;
const SHOP_LNG = 105.8048;
const SHIPPING_RATE_PER_KM = 3000; // 3k/km

let autocomplete;
let geocoder;

// Initialize Google Maps Places Autocomplete
function initAutocomplete() {
    const addressInput = document.getElementById('shipping_address');
    
    if (typeof google !== 'undefined' && google.maps) {
        autocomplete = new google.maps.places.Autocomplete(addressInput, {
            componentRestrictions: { country: 'vn' },
            fields: ['formatted_address', 'geometry', 'name'],
            types: ['address']
        });

        geocoder = new google.maps.Geocoder();

        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (place.geometry) {
                const lat = place.geometry.location.lat();
                const lng = place.geometry.location.lng();
                
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                document.getElementById('shipping_address').value = place.formatted_address;
                
                calculateShippingFromLocation(lat, lng, place.formatted_address);
            }
        });
    }
}

// Display location details
function displayLocationDetails(lat, lng, geocodeResult) {
    const addressInfo = document.getElementById('addressInfo');
    const fullAddressEl = document.getElementById('fullAddress');
    const coordinatesEl = document.getElementById('coordinates');
    
    if (!addressInfo) return;
    
    // Hiển thị tọa độ
    if (coordinatesEl) {
        coordinatesEl.innerHTML = `
            <div class="d-flex align-items-center mb-1">
                <i class="fas fa-globe me-2 text-info"></i>
                <span><strong>Tọa độ:</strong> ${lat.toFixed(6)}, ${lng.toFixed(6)}</span>
            </div>
        `;
    }
    
    // Hiển thị địa chỉ chi tiết nếu có
    if (geocodeResult && fullAddressEl) {
        const addressComponents = geocodeResult.address_components || [];
        let streetNumber = '';
        let route = '';
        let ward = '';
        let district = '';
        let city = '';
        let country = '';
        
        addressComponents.forEach(component => {
            const types = component.types;
            if (types.includes('street_number')) {
                streetNumber = component.long_name;
            } else if (types.includes('route')) {
                route = component.long_name;
            } else if (types.includes('sublocality') || types.includes('sublocality_level_1')) {
                ward = component.long_name;
            } else if (types.includes('administrative_area_level_2')) {
                district = component.long_name;
            } else if (types.includes('administrative_area_level_1')) {
                city = component.long_name;
            } else if (types.includes('country')) {
                country = component.long_name;
            }
        });
        
        let addressParts = [];
        if (streetNumber && route) {
            addressParts.push(`${streetNumber} ${route}`);
        } else if (route) {
            addressParts.push(route);
        }
        if (ward) addressParts.push(ward);
        if (district) addressParts.push(district);
        if (city) addressParts.push(city);
        if (country) addressParts.push(country);
        
        const detailedAddress = addressParts.join(', ');
        
        fullAddressEl.innerHTML = `
            <div class="mb-2">
                <i class="fas fa-map-pin me-2 text-success"></i>
                <strong>Địa chỉ:</strong> ${geocodeResult.formatted_address}
            </div>
            ${detailedAddress ? `
            <div class="mb-2">
                <i class="fas fa-info-circle me-2 text-info"></i>
                <strong>Chi tiết:</strong> ${detailedAddress}
            </div>
            ` : ''}
        `;
    } else if (fullAddressEl) {
        fullAddressEl.innerHTML = `
            <div class="mb-2">
                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                <span>Không thể lấy địa chỉ chi tiết. Vui lòng nhập thủ công.</span>
            </div>
        `;
    }
    
    // Hiển thị container
    addressInfo.style.display = 'block';
}

// Reverse geocode using Nominatim (OpenStreetMap) as fallback
async function reverseGeocodeWithNominatim(lat, lng, addressInput, btn) {
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=vi&zoom=18&addressdetails=1`);
        const data = await response.json();
        
        if (data && data.display_name) {
            const formattedAddress = data.display_name;
            if (addressInput) {
                addressInput.value = formattedAddress;
            }
            
            // Tạo object tương tự Google Geocoder result
            const geocodeResult = {
                formatted_address: formattedAddress,
                address_components: []
            };
            
            if (data.address) {
                const addr = data.address;
                if (addr.house_number) {
                    geocodeResult.address_components.push({
                        types: ['street_number'],
                        long_name: addr.house_number
                    });
                }
                if (addr.road) {
                    geocodeResult.address_components.push({
                        types: ['route'],
                        long_name: addr.road
                    });
                }
                if (addr.suburb || addr.neighbourhood) {
                    geocodeResult.address_components.push({
                        types: ['sublocality'],
                        long_name: addr.suburb || addr.neighbourhood
                    });
                }
                if (addr.city || addr.town || addr.village) {
                    geocodeResult.address_components.push({
                        types: ['administrative_area_level_2'],
                        long_name: addr.city || addr.town || addr.village
                    });
                }
                if (addr.state) {
                    geocodeResult.address_components.push({
                        types: ['administrative_area_level_1'],
                        long_name: addr.state
                    });
                }
                if (addr.country) {
                    geocodeResult.address_components.push({
                        types: ['country'],
                        long_name: addr.country
                    });
                }
            }
            
            displayLocationDetails(lat, lng, geocodeResult);
            calculateShippingFromLocation(lat, lng, formattedAddress);
        } else {
            if (addressInput) {
                addressInput.value = `Vị trí: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            }
            displayLocationDetails(lat, lng, null);
            calculateShippingFromLocation(lat, lng, '');
        }
    } catch (error) {
        console.error('Nominatim geocoding error:', error);
        if (addressInput) {
            addressInput.value = `Vị trí: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        }
        displayLocationDetails(lat, lng, null);
        calculateShippingFromLocation(lat, lng, '');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-crosshairs me-1"></i>Lấy vị trí';
        }
    }
}

// Get current location using browser geolocation
function getCurrentLocation() {
    const btn = document.getElementById('getLocationBtn');
    if (!btn) return;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Đang lấy vị trí...';
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                
                // Reverse geocode to get address
                const addressInput = document.getElementById('shipping_address');
                if (typeof google !== 'undefined' && google.maps) {
                    if (!geocoder) geocoder = new google.maps.Geocoder();
                    geocoder.geocode({ 
                        location: { lat: lat, lng: lng },
                        language: 'vi'
                    }, function(results, status) {
                        if (status === 'OK' && results[0]) {
                            const result = results[0];
                            const formattedAddress = result.formatted_address;
                            
                            // Điền địa chỉ vào ô input
                            if (addressInput) {
                                addressInput.value = formattedAddress;
                            }
                            
                            // Hiển thị thông tin chi tiết
                            displayLocationDetails(lat, lng, result);
                            
                            calculateShippingFromLocation(lat, lng, formattedAddress);
                        } else {
                            // Nếu reverse geocode thất bại, vẫn điền tọa độ vào ô địa chỉ
                            if (addressInput) {
                                addressInput.value = `Vị trí: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                            }
                            
                            // Hiển thị thông tin tọa độ
                            displayLocationDetails(lat, lng, null);
                            
                            calculateShippingFromLocation(lat, lng, '');
                        }
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-crosshairs me-1"></i>Lấy vị trí';
                    });
                } else {
                    // Không có Google Maps, sử dụng Nominatim API
                    reverseGeocodeWithNominatim(lat, lng, addressInput, btn);
                }
            },
            function(error) {
                if (window.notify) window.notify({ type: 'warning', title: 'Vị trí', message: 'Không thể lấy vị trí. Vui lòng nhập địa chỉ thủ công.', duration: 4000 });
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-crosshairs me-1"></i>Lấy vị trí';
            }
        );
    } else {
        if (window.notify) window.notify({ type: 'error', title: 'Vị trí', message: 'Trình duyệt không hỗ trợ lấy vị trí.', duration: 4000 });
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-crosshairs me-1"></i>Lấy vị trí';
    }
}

// Calculate shipping from address or coordinates
function calculateShippingFromLocation(lat, lng, address) {
    const addressInput = document.getElementById('shipping_address');
    const addressValue = address || (addressInput ? addressInput.value.trim() : '');
    
    // Only include address if we have coordinates OR if address is not empty
    const payload = {};
    
    if (lat && lng) {
        // If we have coordinates, address is optional
        if (addressValue) {
            payload.address = addressValue;
        }
        payload.latitude = lat;
        payload.longitude = lng;
    } else if (addressValue) {
        // If no coordinates but we have address, address is required
        payload.address = addressValue;
    } else {
        // Neither coordinates nor address - this should not happen, but handle gracefully
        payload.address = '';
    }

    fetch('{{ route("shipping.calculate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(payload)
    })
    .then(async response => {
        const contentType = response.headers.get('content-type');

        if (!response.ok) {
            const text = await response.text();
            throw new Error(`Server error (${response.status}): ${text.substring(0, 120)}`);
        }

        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            throw new Error('Phản hồi không hợp lệ từ máy chủ: ' + text.substring(0, 120));
        }

        return response.json();
    })
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Không thể tính phí vận chuyển');
        }

        shippingFee = data.shipping_fee;
        
        const addressInfo = document.getElementById('addressInfo');
        const distanceInfo = document.getElementById('distanceInfo');
        
        if (addressInfo && distanceInfo) {
            addressInfo.style.display = 'block';
            if (data.distance !== null) {
                const distance = parseFloat(data.distance);
                const shippingFee = data.shipping_fee || 0;
                distanceInfo.innerHTML = `
                    <div class="d-flex align-items-center mb-1">
                        <i class="fas fa-route me-2 text-primary"></i>
                        <span><strong>Khoảng cách:</strong> ${distance.toFixed(2)} km từ shop</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-shipping-fast me-2 text-success"></i>
                        <span><strong>Phí vận chuyển:</strong> ${shippingFee.toLocaleString('vi-VN')} VNĐ</span>
                    </div>
                `;
                document.getElementById('distance').value = data.distance;
            } else {
                distanceInfo.innerHTML = `
                    <div class="text-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Không xác định được khoảng cách. Sử dụng phí mặc định.
                    </div>
                `;
            }
        }
        
        if (data.latitude) document.getElementById('latitude').value = data.latitude;
        if (data.longitude) document.getElementById('longitude').value = data.longitude;
        
        updateTotals();
    })
    .catch(error => {
        console.error('Error calculating shipping:', error);
        const addressInfo = document.getElementById('addressInfo');
        const distanceInfo = document.getElementById('distanceInfo');
        if (addressInfo && distanceInfo) {
            addressInfo.style.display = 'block';
            distanceInfo.innerHTML = `
                <div class="text-danger">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    Không thể tính khoảng cách tự động. Vui lòng kiểm tra lại địa chỉ hoặc thử lại sau.
                </div>
            `;
        }
    });
}

// Update totals
function updateTotals() {
    const orderTotal = subtotal - voucherDiscount;
    const finalTotal = orderTotal + shippingFee;
    
    const shippingFeeElement = document.getElementById('shippingFeeDisplay');
    if (shippingFeeElement) {
        if (shippingFee > 0) {
            shippingFeeElement.innerHTML = number_format(shippingFee) + ' VNĐ';
        } else {
            shippingFeeElement.innerHTML = '<span class="text-success">Miễn phí</span>';
        }
    }
    
    const finalTotalElement = document.getElementById('finalTotal');
    if (finalTotalElement) {
        finalTotalElement.textContent = number_format(finalTotal) + ' VNĐ';
    }
    
    const voucherDiscountInput = document.getElementById('voucher_discount');
    if (voucherDiscountInput) {
        voucherDiscountInput.value = voucherDiscount;
    }
}

// Select public voucher
function selectPublicVoucher(code, discount) {
    document.getElementById('voucher_code').value = code;
    voucherDiscount = discount;
    
    const messageDiv = document.getElementById('voucherMessage');
    messageDiv.innerHTML = '<div class="alert alert-success">Đã chọn voucher <strong>' + code + '</strong>! Giảm ' + number_format(discount) + ' VNĐ</div>';
    
    document.getElementById('voucherDiscountRow').style.display = 'flex';
    document.getElementById('voucherDiscount').textContent = '-' + number_format(voucherDiscount) + ' VNĐ';
    
    // Highlight selected voucher
    document.querySelectorAll('.voucher-card').forEach(card => {
        card.classList.remove('border-primary', 'bg-light', 'shadow-sm');
    });
    const selectedCard = document.getElementById('voucher-card-' + code);
    if (selectedCard) {
        selectedCard.classList.add('border-primary', 'bg-light', 'shadow-sm');
    }
    
    updateTotals();
}

// Apply voucher
function applyVoucher() {
    const code = document.getElementById('voucher_code').value.trim();
    const messageDiv = document.getElementById('voucherMessage');
    const applyBtn = document.getElementById('applyVoucherBtn');
    
    if (!code) {
        messageDiv.innerHTML = '<div class="alert alert-warning">Vui lòng nhập mã voucher</div>';
        return;
    }
    
    applyBtn.disabled = true;
    applyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Đang kiểm tra...';
    
    fetch('{{ route("cart.apply-voucher") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            code: code,
            order_total: subtotal
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            voucherDiscount = data.discount;
            messageDiv.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
            document.getElementById('voucherDiscountRow').style.display = 'flex';
            document.getElementById('voucherDiscount').textContent = '-' + number_format(voucherDiscount) + ' VNĐ';
            
            // Remove highlight from all vouchers
            document.querySelectorAll('.voucher-card').forEach(card => {
                card.classList.remove('border-primary', 'bg-light');
            });
            
            updateTotals();
        } else {
            voucherDiscount = 0;
            messageDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
            document.getElementById('voucherDiscountRow').style.display = 'none';
            updateTotals();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageDiv.innerHTML = '<div class="alert alert-danger">Có lỗi xảy ra khi kiểm tra voucher</div>';
        voucherDiscount = 0;
        updateTotals();
    })
    .finally(() => {
        applyBtn.disabled = false;
        applyBtn.innerHTML = '<i class="fas fa-check me-1"></i>Áp dụng';
    });
}

// Number format helper
function number_format(number) {
    return new Intl.NumberFormat('vi-VN').format(number);
}

// Make number_format globally available
window.number_format = number_format;

// Remove voucher
function removeVoucher() {
    document.getElementById('voucher_code').value = '';
    voucherDiscount = 0;
    
    const messageDiv = document.getElementById('voucherMessage');
    messageDiv.innerHTML = '';
    
    document.getElementById('voucherDiscountRow').style.display = 'none';
    
    // Remove highlight from all vouchers
    document.querySelectorAll('.voucher-card').forEach(card => {
        card.classList.remove('border-primary', 'bg-light', 'shadow-sm');
    });
    
    updateTotals();
}

// Initialize Google Maps when page loads
if (typeof google !== 'undefined' && google.maps) {
    initAutocomplete();
} else {
    // Wait for Google Maps to load
    window.addEventListener('load', function() {
        if (typeof google !== 'undefined' && google.maps) {
            initAutocomplete();
        }
    });
}

// Calculate shipping when address changes manually
let addressTimeout;
const addressInput = document.getElementById('shipping_address');
if (addressInput) {
    addressInput.addEventListener('input', function() {
        clearTimeout(addressTimeout);
        const address = this.value.trim();
        
        if (address.length > 10) {
            addressTimeout = setTimeout(function() {
                const lat = document.getElementById('latitude').value;
                const lng = document.getElementById('longitude').value;
                
                if (lat && lng) {
                    calculateShippingFromLocation(lat, lng, address);
                } else {
                    // Try to geocode the address
                    calculateShippingFromLocation(null, null, address);
                }
            }, 1000);
        }
    });
}

// Initialize Google Maps when page loads
if (typeof google !== 'undefined' && google.maps) {
    initAutocomplete();
} else {
    // Wait for Google Maps to load
    window.addEventListener('load', function() {
        setTimeout(function() {
            if (typeof google !== 'undefined' && google.maps) {
                initAutocomplete();
            }
        }, 1000);
    });
}

// Initialize shipping fee and display
if (voucherDiscount > 0) {
    const voucherDiscountRow = document.getElementById('voucherDiscountRow');
    const voucherDiscountSpan = document.getElementById('voucherDiscount');
    if (voucherDiscountRow) voucherDiscountRow.style.display = 'flex';
    if (voucherDiscountSpan) voucherDiscountSpan.textContent = '-' + number_format(voucherDiscount) + ' VNĐ';
}
updateTotals();

// Allow Enter key to apply voucher
const voucherCodeInput = document.getElementById('voucher_code');
if (voucherCodeInput) {
    voucherCodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            applyVoucher();
        }
    });
}

// Initialize event listeners - functions are already defined above due to hoisting
(function initEventListeners() {
    function setupListeners() {
        // Get location button
        const getLocationBtn = document.getElementById('getLocationBtn');
        if (getLocationBtn && !getLocationBtn.dataset.listenerAttached) {
            getLocationBtn.addEventListener('click', function(e) {
                e.preventDefault();
                getCurrentLocation();
            });
            getLocationBtn.dataset.listenerAttached = 'true';
        }
        
        // Public voucher cards
        const voucherCards = document.querySelectorAll('.voucher-card[data-voucher-code]');
        voucherCards.forEach(function(card) {
            if (!card.dataset.listenerAttached) {
                card.addEventListener('click', function(e) {
                    e.preventDefault();
                    const code = this.getAttribute('data-voucher-code');
                    const discount = parseFloat(this.getAttribute('data-voucher-discount'));
                    selectPublicVoucher(code, discount);
                });
                card.dataset.listenerAttached = 'true';
            }
        });
    }
    
    // Try immediately if DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupListeners);
    } else {
        setupListeners();
    }
    
    // Also try on window load as fallback
    window.addEventListener('load', setupListeners);
})();

</script>
</div>
@endsection