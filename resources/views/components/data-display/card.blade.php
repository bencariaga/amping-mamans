@props(['tool'])

<a href="{{ $tool['link'] }}" target="_blank" class="tool-box {{ $tool['cardClass'] ?? '' }}">
    <img src="{{ asset('images/about/' . $tool['imageSrc']) }}" alt="{{ $tool['imageAlt'] }}" class="{{ $tool['imageClass'] ?? '' }}">
    <span class="tool-name fw-bold">{{ $tool['title'] }}</span>
</a>
