<div class="recipe-rating-summary">
    @if ($recipe->ratings_count > 0)
        <span class="recipe-rating-stars" aria-hidden="true">
            @for ($star = 1; $star <= 5; $star++)
                <span class="{{ $star <= (int) round($recipe->ratings_avg_rating) ? 'is-filled' : '' }}">★</span>
            @endfor
        </span>
        <span>{{ number_format((float) $recipe->ratings_avg_rating, 1, ',', '') }} / 5 ({{ $recipe->ratings_count }} {{ $recipe->ratings_count === 1 ? 'vērtējums' : 'vērtējumi' }})</span>
    @else
        <span class="recipe-rating-stars" aria-hidden="true">★★★★★</span>
        <span>Vēl nav vērtējumu</span>
    @endif
</div>
