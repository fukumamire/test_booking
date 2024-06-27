<div class="booking__content {{ $loop->iteration % 2 == 0 ? 'booking__content--steelblue' : '' }}">
  <div class="booking__header">
    <a href="{{ route('bookings.show', ['booking' => $booking->id]) }}" class="booking__header__title">
      <span class="header__title">Shop{{ $booking->shop->name }}</span>
    </a>
    <div class="booking__header-button">
      <form method="POST" action="{{ route('bookings.edit', ['booking' => $booking->id]) }}" class="header__form">
        @csrf
        <button type="submit" class="form__button form__button--edit">
          <img src="{{ asset('images/pencil.png') }}" alt="編集" class="form__button-img white-image">
        </button>
      </form>
      <form method="POST" action="{{ route('bookings.cancel', ['booking' => $booking->id]) }}" class="header__form">
        @csrf
        <button type="submit" class="form__button form__button--cancel">
          <img src="{{ asset('images/cancel.png') }}" alt="キャンセル" class="form__button-img white-image">
        </button>
      </form>
    </div>
  </div>
  <div class="booking__details">
    <p>Date: {{ $booking->date }} </p>
    <p>Time:
      @if($booking->time instanceof \DateTime)
        {{ $booking->time->format('H:i') }}
      @else
        {{ date('H:i', strtotime($booking->time)) }}
      @endif
    </p>
    <p>Number: {{ $booking->number_of_people }}人</p>
    {{-- <p>Status: {{ $booking->status }}</p> --}}
  </div>
</div>
