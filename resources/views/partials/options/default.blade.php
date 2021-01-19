<li class="nav-item">
    <a class="nav-link {{ isset($option['active']) ? 'active btn btn-secondary text-white' : '' }}"
    href="{{ $option['url'] }}"
    {{ isset($option['id']) ? 'id=' . $option['id'] : '' }}
    {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}
    rel="noopener noreferrer">
        {{ $option['option'] }}
    </a>
</li>
