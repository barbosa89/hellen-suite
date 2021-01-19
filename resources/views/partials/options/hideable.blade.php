<li class="nav-item">
    <a class="nav-link" href="{{ $option['url'] }}"
    {{ isset($option['id']) ? 'id=' . $option['id'] : '' }}
    {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}
    rel="noopener noreferrer">
        {{ $option['option'] }}
    </a>
</li>
