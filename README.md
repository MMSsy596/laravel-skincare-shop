# BeautyAI Shop - Shop Mỹ Phẩm Online với AI

Một ứng dụng e-commerce hiện đại chuyên về mỹ phẩm với tích hợp AI thông minh để tư vấn sản phẩm và quản lý kho hàng.

## 🌟 Tính năng chính

### 🛍️ E-commerce Cơ bản
- **Quản lý sản phẩm**: CRUD đầy đủ với thông tin chi tiết mỹ phẩm
- **Giỏ hàng thông minh**: Hỗ trợ cả khách và thành viên
- **Thanh toán**: Tích hợp VRPay
- **Đánh giá sản phẩm**: Hệ thống review và rating
- **Tìm kiếm & Lọc**: Theo danh mục, loại da, độ tuổi, giá cả

### 🤖 AI Assistant
- **Chatbot thông minh**: Tư vấn sản phẩm theo loại da
- **Kiểm tra tồn kho**: Hỏi AI về tình trạng hàng
- **Gợi ý sản phẩm**: Dựa trên loại da và nhu cầu
- **Phân tích thành phần**: AI phân tích và đưa ra lời khuyên

### 👩‍💼 Admin Panel
- **Dashboard thống kê**: Tổng quan về sản phẩm, đơn hàng
- **Quản lý sản phẩm**: Giao diện hiện đại với filtering
- **Quản lý đơn hàng**: Theo dõi trạng thái và xử lý
- **Báo cáo**: Thống kê doanh thu và xu hướng

## 🚀 Cài đặt

### Yêu cầu hệ thống
- PHP 8.0+
- Laravel 9
- MySQL/PostgreSQL
- Composer
- Node.js & NPM

### Bước 1: Clone repository
```bash
git clone <repository-url>
cd my-crud-app
```

### Bước 2: Cài đặt dependencies
```bash
composer install
npm install
```

### Bước 3: Cấu hình môi trường
```bash
cp .env.example .env
php artisan key:generate
```

Cấu hình database trong file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=beauty_ai_shop
DB_USERNAME=root
DB_PASSWORD=
```

### Bước 4: Chạy migrations và seeders
```bash
php artisan migrate
php artisan db:seed --class=BeautyProductsSeeder
```

### Bước 5: Tạo storage link
```bash
php artisan storage:link
```

### Bước 6: Build assets
```bash
npm run build
```

### Bước 7: Chạy server
```bash
php artisan serve
```

## 📱 Sử dụng

### Trang chủ
- Truy cập `http://localhost:8000`
- Xem sản phẩm nổi bật và danh mục
- Sử dụng AI chatbot để được tư vấn

### Shop
- Truy cập `/shop` để xem tất cả sản phẩm
- Sử dụng bộ lọc theo danh mục, loại da, giá cả
- Tìm kiếm sản phẩm theo tên hoặc thương hiệu

### AI Assistant
- Click vào icon AI ở góc phải dưới
- Hỏi về sản phẩm phù hợp với loại da
- Kiểm tra tình trạng tồn kho
- Nhận tư vấn về thành phần và cách sử dụng

### Admin Panel
- Truy cập `/admin` (cần đăng nhập với quyền admin)
- Quản lý sản phẩm tại `/admin/products`
- Xem thống kê và báo cáo

## 🎨 Giao diện

### Thiết kế hiện đại
- **Responsive**: Tương thích mọi thiết bị
- **Material Design**: Giao diện đẹp mắt, dễ sử dụng
- **Animations**: Hiệu ứng mượt mà với AOS
- **Color Scheme**: Bảng màu chuyên nghiệp cho mỹ phẩm

### Components
- **Hero Section**: Banner chính với call-to-action
- **Product Cards**: Hiển thị sản phẩm với rating và quick actions
- **AI Chatbot**: Giao diện chat thân thiện
- **Filter Panel**: Bộ lọc thông minh với AJAX
- **Admin Dashboard**: Thống kê trực quan

## 🤖 AI Features

### Chatbot Commands
```
"da khô" - Tư vấn cho da khô
"da dầu" - Tư vấn cho da dầu
"còn hàng không" - Kiểm tra tồn kho
"giá bao nhiêu" - Thông tin giá
"serum" - Tư vấn về serum
"chống lão hóa" - Sản phẩm chống lão hóa
```

### AI Analysis
- **Skin Type Analysis**: Phân tích loại da và đưa ra gợi ý
- **Ingredient Analysis**: Phân tích thành phần và lợi ích
- **Product Recommendations**: Gợi ý sản phẩm phù hợp
- **Stock Management**: Kiểm tra và cảnh báo tồn kho

## 📊 Database Schema

### Products Table
```sql
- id, name, description, price
- category, brand, sku, stock
- skin_type, age_group, ingredients
- usage_instructions, shelf_life, weight, dimensions
- is_featured, is_active, image
- created_at, updated_at
```

### Categories
- **skincare**: Chăm sóc da
- **makeup**: Trang điểm
- **perfume**: Nước hoa
- **haircare**: Chăm sóc tóc
- **bodycare**: Chăm sóc cơ thể
- **tools**: Dụng cụ làm đẹp

### Skin Types
- **normal**: Da thường
- **dry**: Da khô
- **oily**: Da dầu
- **combination**: Da hỗn hợp
- **sensitive**: Da nhạy cảm
- **acne-prone**: Da mụn
- **mature**: Da trưởng thành

## 🔧 API Endpoints

### AI Endpoints
```
GET /ai/recommendations - Gợi ý sản phẩm
GET /ai/stock-check - Kiểm tra tồn kho
GET /ai/skin-analysis - Phân tích loại da
GET /ai/product-analysis - Phân tích sản phẩm
GET /ai/trending - Sản phẩm trending
GET /ai/personalized - Gợi ý cá nhân hóa
```

### Product Endpoints
```
GET /shop - Danh sách sản phẩm
GET /products/{id} - Chi tiết sản phẩm
POST /cart/add - Thêm vào giỏ hàng
GET /cart - Xem giỏ hàng
```

## 🛠️ Development

### Cấu trúc thư mục
```
app/
├── Http/Controllers/
│   ├── AIController.php
│   ├── ProductController.php
│   ├── CartController.php
│   └── Admin/
│       └── ProductController.php
├── Models/
│   ├── Product.php
│   ├── User.php
│   └── Review.php
└── ...

resources/views/
├── layouts/
│   └── app.blade.php
├── shop.blade.php
├── products/
│   └── show.blade.php
└── admin/products/
    ├── index.blade.php
    ├── create.blade.php
    └── edit.blade.php
```

### Customization
1. **Thay đổi theme**: Chỉnh sửa CSS variables trong `app.blade.php`
2. **Thêm danh mục**: Cập nhật `Product::CATEGORIES`
3. **Mở rộng AI**: Thêm logic trong `AIController`
4. **Tùy chỉnh chatbot**: Chỉnh sửa responses trong `generateAIResponse()`

## 📈 Performance

### Optimization
- **Lazy Loading**: Images và components
- **Caching**: Product data và AI responses
- **Pagination**: Danh sách sản phẩm
- **CDN**: Static assets

### Monitoring
- **Error Tracking**: Laravel Telescope
- **Performance**: Laravel Debugbar
- **Logs**: Application và AI interactions

## 🔒 Security

### Authentication
- Laravel Breeze
- Google OAuth (Socialite)
- Role-based access control

### Data Protection
- CSRF protection
- SQL injection prevention
- XSS protection
- File upload validation

## 🚀 Deployment

### Production Setup
1. Cấu hình environment variables
2. Optimize autoloader: `composer install --optimize-autoloader --no-dev`
3. Cache config: `php artisan config:cache`
4. Cache routes: `php artisan route:cache`
5. Cache views: `php artisan view:cache`

### Server Requirements
- PHP 8.0+
- MySQL 5.7+ hoặc PostgreSQL 10+
- Redis (optional, for caching)
- SSL certificate (recommended)

## 🤝 Contributing

1. Fork repository
2. Tạo feature branch: `git checkout -b feature/new-feature`
3. Commit changes: `git commit -am 'Add new feature'`
4. Push branch: `git push origin feature/new-feature`
5. Tạo Pull Request

## 📄 License

MIT License - xem file LICENSE để biết thêm chi tiết.

## 📞 Support

- **Email**: support@beauty-ai-shop.com
- **Documentation**: `/docs`
- **Issues**: GitHub Issues
- **Discord**: BeautyAI Community

---

**BeautyAI Shop** - Nơi mỹ phẩm gặp gỡ trí tuệ nhân tạo! ✨
