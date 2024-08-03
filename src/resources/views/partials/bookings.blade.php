<div class="booking__content {{ $loop->iteration % 2 == 0 ? 'booking__content--steelblue' : '' }}">
  <div class="booking__header">
    <span class="header__title">
      <img src="{{ asset('images/clock.png') }}" alt="時計" class="clock-icon white-image"> 予約{{ $loop->iteration }}
      </span>
      <a href="{{ route('bookings.show', ['booking' => $booking->id]) }}" class="booking__header__title">
    </a>

    <div class="booking__header-button">
      {{-- 予約変更ボタンの設定 --}}
      <button type="button" class="form__button form__button--change" data-id="{{ $booking->id }}" onclick="openModal({!! $booking->id !!)"><img src="{{ asset('images/pencil.png') }}" alt="変更" class="form__button-img white-image"></button>
      
      <form id="cancelForm{{ $booking->id }}" method="POST" action="{{ route('bookings.cancel', ['booking' => $booking->id]) }}" class="header__form">
        @csrf
        @method('DELETE')
        <button type="button" class="form__button form__button--cancel" data-id="{{ $booking->id }}">
          <img src="{{ asset('images/cancel.png') }}" alt="キャンセル" class="form__button-img white-image">
        </button>
      </form>
    </div>
  </div>
  <div class="booking__details">
    <p><span class="shop-label">Shop </span>{{ $booking->shop->name }}</p>
    <p><span class="date-label">Date</span> {{ $booking->date }} </p>
    <p><span class="time-label">Time</span>
      @if($booking->time instanceof \DateTime)
        {{ $booking->time->format('H:i') }}
      @else
        {{ date('H:i', strtotime($booking->time)) }}
      @endif
    </p>
    <p><span class="number-label">Number</span> {{ $booking->number_of_people }}人</p>
  </div>
</div>


@section('script')
<script src="{{ asset('js/cancelBooking.js') }}"></script>
@endsection

