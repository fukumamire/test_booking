{{-- <div class="shop-item">
    <h3>{{ $shop->name }}</h3>
    <p>{{ $shop->outline }}</p>
    @foreach($shop->images as $image)
        <img src="{{ $image->shop_image_url }}" alt="{{ $shop->name }}" class="shop-image">
    @endforeach
    @if(isset($favorites) && in_array($shop->id, $favorites))
        <p>お気に入り登録済み</p>
    @else
        <p>お気に入り登録されていません</p>
    @endif
</div> --}}


{{-- <div class="shop-item">
    <h3>{{ $shop->name }}</h3>
    <p>{{ $shop->outline }}</p>
    @foreach($shop->images as $image)
        <img src="{{ $image->shop_image_url }}" alt="{{ $shop->name }}" class="shop-image">
    @endforeach
    @if(in_array($shop->id, $favorites))
        <p>お気に入り登録済み</p>
    @else
        <p>お気に入り登録されていません</p>
    @endif
</div> --}}