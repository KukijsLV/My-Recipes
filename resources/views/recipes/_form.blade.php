@csrf
<div class="form-field">
    <label for="title">Nosaukums</label>
    <input id="title" name="title" type="text" value="{{ old('title', $recipe->title ?? '') }}" required>
</div>
<div class="form-field">
    <label for="description">Apraksts</label>
    <textarea id="description" name="description" rows="3">{{ old('description', $recipe->description ?? '') }}</textarea>
</div>
<div class="form-field">
    <label for="ingredients">Sastāvdaļas</label>
    <textarea id="ingredients" name="ingredients" rows="6" required>{{ old('ingredients', $recipe->ingredients ?? '') }}</textarea>
</div>
<div class="form-field">
    <label for="instructions">Pagatavošana</label>
    <textarea id="instructions" name="instructions" rows="6" required>{{ old('instructions', $recipe->instructions ?? '') }}</textarea>
</div>
<div class="form-field">
    <label for="image">Uzņemt vai izvēlēties attēlu</label>
    <input id="image" name="image" type="file" accept="image/*" capture="environment">
    <small class="form-hint">Telefonā atvērsies aizmugurējā kamera. Maksimālais faila izmērs: 5 MB.</small>
</div>
<div class="form-field">
    <label for="image_url">Vai ievadīt attēla URL</label>
    <input id="image_url" name="image_url" type="url" value="{{ old('image_url', isset($recipe) && filter_var($recipe->image, FILTER_VALIDATE_URL) ? $recipe->image : '') }}">
    @if (isset($recipe) && $recipe->image)
        <img class="recipe-image-preview" src="{{ $recipe->imageUrl }}" alt="{{ $recipe->title }}">
    @endif
</div>
<fieldset class="visibility-field">
    <legend>Redzamība</legend>
    <div>
        @foreach (['private' => 'Privāta', 'public' => 'Publiska'] as $value => $label)
            <label><input type="radio" name="visibility" value="{{ $value }}" @checked(old('visibility', $recipe->visibility ?? 'private') === $value)> {{ $label }}</label>
        @endforeach
    </div>
</fieldset>
<button class="primary-action" type="submit">{{ $submitLabel }}</button>
