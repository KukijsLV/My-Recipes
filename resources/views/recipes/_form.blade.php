@csrf
<div>
    <label for="title">Nosaukums</label>
    <input id="title" name="title" type="text" value="{{ old('title', $recipe->title ?? '') }}" required>
</div>
<div>
    <label for="description">Apraksts</label>
    <textarea id="description" name="description" rows="3">{{ old('description', $recipe->description ?? '') }}</textarea>
</div>
<div>
    <label for="ingredients">Sastāvdaļas</label>
    <textarea id="ingredients" name="ingredients" rows="6" required>{{ old('ingredients', $recipe->ingredients ?? '') }}</textarea>
</div>
<div>
    <label for="instructions">Pagatavošana</label>
    <textarea id="instructions" name="instructions" rows="6" required>{{ old('instructions', $recipe->instructions ?? '') }}</textarea>
</div>
<div>
    <label for="image">Attēla URL (neobligāts)</label>
    <input id="image" name="image" type="url" value="{{ old('image', $recipe->image ?? '') }}">
</div>
<fieldset>
    <legend>Redzamība</legend>
    <div>
        @foreach (['private' => 'Privāta', 'public' => 'Publiska'] as $value => $label)
            <label><input type="radio" name="visibility" value="{{ $value }}" @checked(old('visibility', $recipe->visibility ?? 'private') === $value)> {{ $label }}</label>
        @endforeach
    </div>
</fieldset>
<button type="submit">{{ $submitLabel }}</button>
