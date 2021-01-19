<a class="dropdown-item" href="{{ $option['url'] }}"
{{ isset($option['id']) ? 'id=' . $option['id'] : '' }}
{{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>
    {{ $option['option'] }}
</a>
