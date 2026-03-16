@extends('layouts.app')

@section('title', 'Về Chúng Tôi')

@section('content')
<div class="bg-[#080808] border-b border-[#1a1a1a] py-20 text-center">
    <div class="max-w-3xl mx-auto px-4">
        <div class="section-label text-center mb-4">Về CoolWear</div>
        <h1 class="section-title text-5xl mb-6">Đam Mê và Phong Cách<br><span class="text-[#C5A572]">Là Tất Cả Của Chúng Tôi</span></h1>
        <p class="text-gray-400 text-lg leading-relaxed">CoolWear được thành lập năm 2015 với sứ mệnh mang đến những bộ trang phục chất lượng cao, thiết kế độc đáo cho người trẻ Việt Nam.</p>
    </div>
</div>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="grid lg:grid-cols-2 gap-16 items-center mb-20">
        <div>
            <div class="section-label mb-4">Câu Chuyện</div>
            <h2 class="section-title mb-6">10 Năm Xây Dựng<br><span class="text-[#C5A572]">Thương Hiệu</span></h2>
            <div class="gold-divider"></div>
            <p class="text-gray-400 mb-4 leading-relaxed">Từ một cửa hàng nhỏ ở Hà Nội, CoolWear ngày nay đã phục vụ hơn 50.000 khách hàng trên toàn quốc. Chúng tôi không chỉ bán quần áo – chúng tôi giúp bạn kể câu chuyện của chính mình qua thời trang.</p>
            <p class="text-gray-400 leading-relaxed">Mỗi sản phẩm đều được nghiên cứu kỹ lưỡng về chất liệu, đường may, và thiết kế để đảm bảo bạn luôn tự tin và thoải mái nhất.</p>
        </div>
        <div class="aspect-[4/3] bg-[#111] overflow-hidden">
            <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=700&q=80" alt="CoolWear Story" class="w-full h-full object-cover">
        </div>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach([['50K+','Khách hàng'],['500+','Sản phẩm'],['10+','Năm kinh nghiệm'],['4.9★','Đánh giá TB']] as [$num,$label])
        <div class="bg-[#111] border border-[#1a1a1a] p-8 text-center hover:border-[#C5A572] transition-colors">
            <div class="font-[Outfit] font-bold text-3xl text-[#C5A572] mb-2">{{ $num }}</div>
            <div class="text-gray-400 text-sm uppercase tracking-wide">{{ $label }}</div>
        </div>
        @endforeach
    </div>
</div>
@endsection
