@php
    /** @var \App\Models\PpdbField $field */
    $name = $field->key;
    $value = old($name);
    $required = $field->is_required;
    $wrapper = $field->width === 'full' ? 'sm:col-span-2' : '';
    $inputType = in_array($field->type, ['number', 'email', 'tel', 'date'], true) ? $field->type : 'text';
@endphp

<div class="{{ $wrapper }}">
    <label class="ppdb-label" for="ppdb-{{ $name }}">
        {{ $field->label }}@if($required) <span class="ppdb-required">*</span>@endif
    </label>

    @switch($field->type)
        @case('textarea')
            <textarea id="ppdb-{{ $name }}" name="{{ $name }}" rows="3"
                      class="ppdb-input @error($name) border-red-400 @enderror"
                      placeholder="{{ $field->placeholder }}" @if($required) required @endif>{{ $value }}</textarea>
            @break

        @case('select')
            <select id="ppdb-{{ $name }}" name="{{ $name }}"
                    class="ppdb-input @error($name) border-red-400 @enderror" @if($required) required @endif>
                <option value="">— Pilih —</option>
                @foreach($field->optionValues() as $opt)
                    <option value="{{ $opt }}" @selected((string) $value === (string) $opt)>{{ $opt }}</option>
                @endforeach
            </select>
            @break

        @case('radio')
            <div class="space-y-2 mt-1">
                @foreach($field->optionValues() as $opt)
                <label class="flex items-center gap-2.5 text-sm cursor-pointer" style="color:var(--text)">
                    <input type="radio" name="{{ $name }}" value="{{ $opt }}"
                           @checked((string) $value === (string) $opt) @if($required) required @endif
                           style="accent-color:#d97706">
                    <span>{{ $opt }}</span>
                </label>
                @endforeach
            </div>
            @break

        @case('file')
            <input type="file" id="ppdb-{{ $name }}" name="{{ $name }}"
                   class="ppdb-input @error($name) border-red-400 @enderror"
                   accept=".jpg,.jpeg,.png,.pdf" @if($required) required @endif>
            <p class="ppdb-hint">Format: JPG, PNG, atau PDF. Maksimal 2 MB.</p>
            @break

        @default
            <input type="{{ $inputType }}" id="ppdb-{{ $name }}" name="{{ $name }}"
                   class="ppdb-input @error($name) border-red-400 @enderror"
                   value="{{ $value }}" placeholder="{{ $field->placeholder }}"
                   @if($required) required @endif
                   @if($name === 'nik') inputmode="numeric" maxlength="16" @endif>
    @endswitch

    @if($field->help_text)<p class="ppdb-hint">{{ $field->help_text }}</p>@endif
    @error($name)<p class="ppdb-hint text-red-500">{{ $message }}</p>@enderror
</div>
