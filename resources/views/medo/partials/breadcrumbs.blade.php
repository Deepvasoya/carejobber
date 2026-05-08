<nav aria-label="breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    @foreach($items as $item)
      <li class="breadcrumb-item{{ $item['url'] ? '' : ' active' }}"@if(!$item['url']) aria-current="page"@endif>
        @if($item['url'])
          <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
        @else
          {{ $item['label'] }}
        @endif
      </li>
    @endforeach
  </ol>
</nav>
