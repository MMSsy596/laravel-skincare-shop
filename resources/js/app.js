import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const di = (() => {
  let el, titleEl, messageEl, queue = [], busy = false;
  function init() {
    el = document.getElementById('dynamicIsland');
    if (!el) return;
    titleEl = el.querySelector('.di-title');
    messageEl = el.querySelector('.di-message');
  }
  function show(payload) {
    if (!el) return;
    queue.push(payload);
    if (!busy) process();
  }
  function process() {
    if (!el) return;
    if (queue.length === 0) { busy = false; return; }
    busy = true;
    const item = queue.shift();
    const type = item.type || 'info';
    const title = item.title || 'Thông báo';
    const message = item.message || '';
    const duration = item.duration || 3000;
    el.classList.remove('info','success','warning','error');
    el.classList.add(type);
    if (titleEl) titleEl.textContent = title;
    if (messageEl) messageEl.textContent = message;
    el.classList.add('expanded');
    el.classList.remove('collapsed');
    clearTimeout(el._timer);
    el._timer = setTimeout(() => {
      el.classList.remove('expanded');
      el.classList.add('collapsed');
      setTimeout(() => { busy = false; process(); }, 300);
    }, duration);
  }
  document.addEventListener('DOMContentLoaded', () => {
    init();
    const flashes = window.__flashes || [];
    flashes.forEach(f => show(f));
    const island = document.getElementById('dynamicIsland');
    if (island) {
      island.addEventListener('click', () => {
        const panel = document.getElementById('diSettingsPanel');
        const isShown = panel && panel.classList.contains('show');
        toggleDiSettings(!isShown);
      });
    }
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) applyTheme(savedTheme);
    const savedLang = localStorage.getItem('locale');
    if (savedLang) applyLanguage(savedLang); else applyTranslations();
  });
  return { show };
})();

window.notify = (payload) => di.show(payload);
window.addEventListener('notify', e => di.show(e.detail || {}));

const i18n = {
  vi: {
    'settings.title':'Cài đặt','settings.theme':'Giao diện','settings.language':'Ngôn ngữ','settings.light':'Sáng','settings.dark':'Tối',
    'nav.shop':'Mỹ phẩm','nav.categories':'Danh mục','nav.ai':'Tư vấn AI','nav.cart':'Giỏ hàng','nav.admin':'Quản trị','nav.account':'Tài khoản','nav.shop_view':'Xem trang Shop',
    'auth.login':'Đăng nhập','auth.register':'Đăng ký','profile':'Thông tin cá nhân','orders.history':'Lịch sử đơn hàng','reviews.my':'Đánh giá của tôi',
    'admin.dashboard':'Dashboard','admin.products':'Quản lý sản phẩm','admin.orders':'Quản lý đơn hàng','admin.reviews':'Quản lý đánh giá','admin.vouchers':'Quản lý voucher',
    'search.submit':'Tìm kiếm','refresh':'Làm mới','export.excel':'Xuất Excel','bulk.activate':'Kích hoạt hàng loạt',
    'shop.sort.price_asc':'Giá tăng dần','shop.sort.price_desc':'Giá giảm dần','shop.sort.name_asc':'Tên A-Z','shop.sort.name_desc':'Tên Z-A','shop.sort.rating':'Đánh giá cao nhất','shop.sort.popular':'Phổ biến nhất','shop.sort.newest':'Mới nhất'
  },
  en: {
    'settings.title':'Settings','settings.theme':'Theme','settings.language':'Language','settings.light':'Light','settings.dark':'Dark',
    'nav.shop':'Shop','nav.categories':'Categories','nav.ai':'AI Assistant','nav.cart':'Cart','nav.admin':'Admin','nav.account':'Account','nav.shop_view':'View Shop',
    'auth.login':'Login','auth.register':'Register','profile':'Profile','orders.history':'Order History','reviews.my':'My Reviews',
    'admin.dashboard':'Dashboard','admin.products':'Products','admin.orders':'Orders','admin.reviews':'Reviews','admin.vouchers':'Vouchers',
    'search.submit':'Search','refresh':'Refresh','export.excel':'Export Excel','bulk.activate':'Bulk Activate',
    'shop.sort.price_asc':'Price Asc','shop.sort.price_desc':'Price Desc','shop.sort.name_asc':'Name A-Z','shop.sort.name_desc':'Name Z-A','shop.sort.rating':'Top Rated','shop.sort.popular':'Most Popular','shop.sort.newest':'Newest'
  },
  zh: {
    'settings.title':'设置','settings.theme':'主题','settings.language':'语言','settings.light':'浅色','settings.dark':'深色',
    'nav.shop':'商店','nav.categories':'分类','nav.ai':'AI 助手','nav.cart':'购物车','nav.admin':'管理','nav.account':'账户','nav.shop_view':'查看商店',
    'auth.login':'登录','auth.register':'注册','profile':'个人资料','orders.history':'订单历史','reviews.my':'我的评价',
    'admin.dashboard':'仪表盘','admin.products':'产品','admin.orders':'订单','admin.reviews':'评价','admin.vouchers':'优惠券',
    'search.submit':'搜索','refresh':'刷新','export.excel':'导出 Excel','bulk.activate':'批量启用',
    'shop.sort.price_asc':'价格升序','shop.sort.price_desc':'价格降序','shop.sort.name_asc':'名称 A-Z','shop.sort.name_desc':'名称 Z-A','shop.sort.rating':'评分最高','shop.sort.popular':'最受欢迎','shop.sort.newest':'最新'
  },
  ko: {
    'settings.title':'설정','settings.theme':'테마','settings.language':'언어','settings.light':'라이트','settings.dark':'다크',
    'nav.shop':'샵','nav.categories':'카테고리','nav.ai':'AI 도우미','nav.cart':'장바구니','nav.admin':'관리','nav.account':'계정','nav.shop_view':'샵 보기',
    'auth.login':'로그인','auth.register':'회원가입','profile':'프로필','orders.history':'주문 내역','reviews.my':'내 리뷰',
    'admin.dashboard':'대시보드','admin.products':'상품','admin.orders':'주문','admin.reviews':'리뷰','admin.vouchers':'바우처',
    'search.submit':'검색','refresh':'새로고침','export.excel':'엑셀 내보내기','bulk.activate':'일괄 활성화',
    'shop.sort.price_asc':'가격 오름차순','shop.sort.price_desc':'가격 내림차순','shop.sort.name_asc':'이름 A-Z','shop.sort.name_desc':'이름 Z-A','shop.sort.rating':'평점 높은','shop.sort.popular':'인기 많은','shop.sort.newest':'최신'
  },
  ja: {
    'settings.title':'設定','settings.theme':'テーマ','settings.language':'言語','settings.light':'ライト','settings.dark':'ダーク',
    'nav.shop':'ショップ','nav.categories':'カテゴリ','nav.ai':'AIアシスタント','nav.cart':'カート','nav.admin':'管理','nav.account':'アカウント','nav.shop_view':'ショップを見る',
    'auth.login':'ログイン','auth.register':'登録','profile':'プロフィール','orders.history':'注文履歴','reviews.my':'私のレビュー',
    'admin.dashboard':'ダッシュボード','admin.products':'商品','admin.orders':'注文','admin.reviews':'レビュー','admin.vouchers':'クーポン',
    'search.submit':'検索','refresh':'更新','export.excel':'Excel出力','bulk.activate':'一括有効化',
    'shop.sort.price_asc':'価格昇順','shop.sort.price_desc':'価格降順','shop.sort.name_asc':'名前 A-Z','shop.sort.name_desc':'名前 Z-A','shop.sort.rating':'最高評価','shop.sort.popular':'人気','shop.sort.newest':'最新'
  },
  // Footer translations
  vi_footer: {
    'footer.categories':'Danh mục','footer.support':'Hỗ trợ','footer.subscribe':'Đăng ký nhận tin','footer.subscribe.desc':'Nhận thông tin về sản phẩm mới và khuyến mãi đặc biệt','footer.subscribe.placeholder':'Email của bạn','footer.subscribe.button':'Đăng ký','footer.built':'Được phát triển với','footer.and_ai':'và AI',
    'footer.cats.skin':'Chăm sóc da','footer.cats.makeup':'Trang điểm','footer.cats.perfume':'Nước hoa','footer.cats.hair':'Chăm sóc tóc',
    'footer.support.ai':'Tư vấn AI','footer.support.guide':'Hướng dẫn mua','footer.support.return':'Chính sách đổi trả','footer.support.contact':'Liên hệ'
  },
  en_footer: {
    'footer.categories':'Categories','footer.support':'Support','footer.subscribe':'Subscribe','footer.subscribe.desc':'Get updates on new products and special offers','footer.subscribe.placeholder':'Your email','footer.subscribe.button':'Subscribe','footer.built':'Built with','footer.and_ai':'and AI',
    'footer.cats.skin':'Skincare','footer.cats.makeup':'Makeup','footer.cats.perfume':'Perfume','footer.cats.hair':'Haircare',
    'footer.support.ai':'AI Assistant','footer.support.guide':'Buying Guide','footer.support.return':'Return Policy','footer.support.contact':'Contact'
  },
  zh_footer: {
    'footer.categories':'分类','footer.support':'支持','footer.subscribe':'订阅','footer.subscribe.desc':'获取新品和特别优惠信息','footer.subscribe.placeholder':'你的邮箱','footer.subscribe.button':'订阅','footer.built':'由…开发','footer.and_ai':'与 AI',
    'footer.cats.skin':'护肤','footer.cats.makeup':'彩妆','footer.cats.perfume':'香水','footer.cats.hair':'护发',
    'footer.support.ai':'AI 助手','footer.support.guide':'购买指南','footer.support.return':'退换政策','footer.support.contact':'联系'
  },
  ko_footer: {
    'footer.categories':'카테고리','footer.support':'지원','footer.subscribe':'구독','footer.subscribe.desc':'신제품 및 특별 할인 정보를 받으세요','footer.subscribe.placeholder':'이메일','footer.subscribe.button':'구독','footer.built':'다음으로 개발됨','footer.and_ai':'그리고 AI',
    'footer.cats.skin':'스킨케어','footer.cats.makeup':'메이크업','footer.cats.perfume':'향수','footer.cats.hair':'헤어케어',
    'footer.support.ai':'AI 도우미','footer.support.guide':'구매 가이드','footer.support.return':'반품 정책','footer.support.contact':'문의'
  },
  ja_footer: {
    'footer.categories':'カテゴリ','footer.support':'サポート','footer.subscribe':'購読','footer.subscribe.desc':'新商品と特別セールの情報を入手','footer.subscribe.placeholder':'メールアドレス','footer.subscribe.button':'購読','footer.built':'で作られた','footer.and_ai':'と AI',
    'footer.cats.skin':'スキンケア','footer.cats.makeup':'メイクアップ','footer.cats.perfume':'香水','footer.cats.hair':'ヘアケア',
    'footer.support.ai':'AI アシスタント','footer.support.guide':'購入ガイド','footer.support.return':'返品ポリシー','footer.support.contact':'お問い合わせ'
  }
};

function applyTranslations() {
  const locale = localStorage.getItem('locale') || 'vi';
  const dict = i18n[locale] || i18n.vi;
  const dictFooter = i18n[`${locale}_footer`] || i18n['vi_footer'];
  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.getAttribute('data-i18n');
    if (dict[key]) el.textContent = dict[key];
    else if (dictFooter[key]) el.textContent = dictFooter[key];
  });
}

function toggleDiSettings(show) {
  const panel = document.getElementById('diSettingsPanel');
  if (!panel) return;
  panel.classList.toggle('show', !!show);
}

function applyTheme(theme) {
  document.body.classList.toggle('dark-mode', theme === 'dark');
  document.documentElement.setAttribute('data-bs-theme', theme === 'dark' ? 'dark' : 'light');
}

function setTheme(theme) {
  localStorage.setItem('theme', theme);
  applyTheme(theme);
  if (window.notify) {
    const titles = { vi: 'Giao diện', en: 'Theme', zh: '主题', ko: '테마', ja: 'テーマ' };
    const messages = { vi: theme==='dark'?'Đã chuyển sang nền tối':'Đã chuyển sang nền sáng', en: theme==='dark'?'Switched to dark mode':'Switched to light mode', zh: theme==='dark'?'已切换到深色模式':'已切换到浅色模式', ko: theme==='dark'?'다크 모드로 전환':'라이트 모드로 전환', ja: theme==='dark'?'ダークモードに切替':'ライトモードに切替' };
    const lang = localStorage.getItem('locale') || 'vi';
    window.notify({ type: 'success', title: titles[lang], message: messages[lang], duration: 2500 });
  }
}

function applyLanguage(locale) {
  const select = document.getElementById('diLanguageSelect');
  if (select) select.value = locale;
  window.locale = locale;
  applyTranslations();
}

function setLanguage(locale) {
  localStorage.setItem('locale', locale);
  applyLanguage(locale);
  if (window.notify) {
    const titles = { vi: 'Ngôn ngữ', en: 'Language', zh: '语言', ko: '언어', ja: '言語' };
    const messages = { vi: 'Đã chuyển ngôn ngữ', en: 'Language changed', zh: '语言已更改', ko: '언어가 변경되었습니다', ja: '言語が変更されました' };
    window.notify({ type: 'success', title: titles[locale], message: messages[locale], duration: 2500 });
  }
}

window.toggleDiSettings = toggleDiSettings;
window.setTheme = setTheme;
window.setLanguage = setLanguage;
