@extends('layouts.admin')

@section('title', $page->exists ? 'Edit '.$page->name : 'Add page')
@section('portal-heading', $page->exists ? 'Edit page' : 'Add page')

@section('content')
    <div class="admin-heading">
        <div>
            <p class="eyebrow">Website / Pages</p>
            <h1>{{ $page->exists ? 'Edit '.$page->name : 'Add page' }}</h1>
            <p>Keep content editable while the frontend structure remains protected.</p>
        </div>
        @if($page->exists)
            <a class="button button-light" href="{{ URL::temporarySignedRoute('admin.preview', now()->addMinutes(30), ['type' => 'page', 'id' => $page->id]) }}" target="_blank" rel="noopener">
                Preview page <span aria-hidden="true">↗</span>
            </a>
        @endif
    </div>

    <form class="admin-form" method="post" action="{{ $action }}">
        @csrf
        @if($page->exists) @method('put') @endif

        <fieldset class="form-section">
            <legend>Page identity</legend>
            <div class="form-row">
                <label>Name
                    <input name="name" value="{{ old('name', $page->name) }}" required>
                </label>
                <label>Key
                    <input name="key" value="{{ old('key', $page->key) }}" required @if($page->exists) readonly @endif>
                </label>
            </div>
            <div class="form-row">
                <label>Slug
                    <input name="slug" value="{{ old('slug', $page->slug) }}" placeholder="Optional public slug">
                </label>
                <label>Page type
                    <select name="page_type" required>
                        @foreach(['home', 'index', 'standard', 'form', 'policy'] as $type)
                            <option value="{{ $type }}" @selected(old('page_type', $page->page_type) === $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="form-row">
                <label>Status
                    <select name="status" required>
                        @foreach(['draft', 'published', 'archived'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $page->status) === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Published at
                    <input type="datetime-local" name="published_at" value="{{ old('published_at', $page->published_at?->format('Y-m-d\TH:i')) }}">
                </label>
            </div>
            <label class="checkbox-field">
                <input type="checkbox" name="is_visible" value="1" @checked(old('is_visible', $page->is_visible))>
                Visible in the public system
            </label>
        </fieldset>

        <fieldset class="form-section">
            <legend>Hero</legend>
            <div class="form-row">
                <label>Eyebrow
                    <input name="hero_eyebrow" value="{{ old('hero_eyebrow', $page->hero_eyebrow) }}">
                </label>
                <label>Title
                    <input name="hero_title" value="{{ old('hero_title', $page->hero_title) }}">
                </label>
            </div>
            <div class="form-row">
                <label>Highlighted title line
                    <input name="hero_highlight" value="{{ old('hero_highlight', $page->hero_highlight) }}">
                </label>
                <label>Hero note
                    <input name="hero_note" value="{{ old('hero_note', $page->hero_note) }}" placeholder="Use • between short points">
                </label>
            </div>
            <label>Summary
                <textarea name="hero_summary" rows="3">{{ old('hero_summary', $page->hero_summary) }}</textarea>
            </label>
            @include('admin.components.media-field', ['name' => 'hero_image', 'label' => 'Hero image', 'value' => $page->hero_image, 'media' => $media])
            <div class="form-row">
                <label>Primary CTA label
                    <input name="hero_primary_label" value="{{ old('hero_primary_label', $page->hero_primary_label) }}">
                </label>
                <label>Primary CTA URL
                    <input name="hero_primary_url" value="{{ old('hero_primary_url', $page->hero_primary_url) }}">
                </label>
            </div>
            <div class="form-row">
                <label>Secondary CTA label
                    <input name="hero_secondary_label" value="{{ old('hero_secondary_label', $page->hero_secondary_label) }}">
                </label>
                <label>Secondary CTA URL
                    <input name="hero_secondary_url" value="{{ old('hero_secondary_url', $page->hero_secondary_url) }}">
                </label>
            </div>
            <div class="form-row">
                <label>Hero aside
                    <textarea name="hero_aside" rows="2" placeholder="Use a new line for the second line">{{ old('hero_aside', $page->hero_aside) }}</textarea>
                </label>
                <label>Hero aside index
                    <input name="hero_aside_index" value="{{ old('hero_aside_index', $page->hero_aside_index) }}" placeholder="01 / 04">
                </label>
            </div>
            <div class="form-row">
                <label>Overlay
                    <select name="hero_overlay">
                        @foreach(['light', 'medium', 'strong'] as $overlay)
                            <option value="{{ $overlay }}" @selected(old('hero_overlay', $page->hero_overlay) === $overlay)>{{ ucfirst($overlay) }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Alignment
                    <select name="hero_alignment">
                        @foreach(['left', 'center', 'right'] as $alignment)
                            <option value="{{ $alignment }}" @selected(old('hero_alignment', $page->hero_alignment) === $alignment)>{{ ucfirst($alignment) }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </fieldset>

        <fieldset class="form-section">
            <legend>SEO</legend>
            <label>SEO title
                <input name="seo_title" value="{{ old('seo_title', $page->seo_title) }}">
            </label>
            <label>SEO description
                <textarea name="seo_description" rows="3">{{ old('seo_description', $page->seo_description) }}</textarea>
            </label>
        </fieldset>

        @if($page->exists && $page->sections->isNotEmpty())
            <fieldset class="form-section">
                <legend>Structured sections</legend>
                <p class="field-help">Only registered section fields are editable here. Layout, breakpoints and component behavior remain controlled by the application.</p>

                @foreach($page->sections as $section)
                    @php($sectionPayload = $section->payload ?: [])
                    <div class="cms-section-editor">
                        <div class="cms-section-editor-head">
                            <strong>{{ $section->key }}</strong>
                            <span>{{ $section->section_type }}</span>
                        </div>

                        <div class="form-row">
                            <label>Heading
                                <input name="sections[{{ $section->id }}][heading]" value="{{ old('sections.'.$section->id.'.heading', $section->heading) }}">
                            </label>
                            <label>Order
                                <input type="number" min="0" name="sections[{{ $section->id }}][sort_order]" value="{{ old('sections.'.$section->id.'.sort_order', $section->sort_order) }}">
                            </label>
                        </div>

                        <label>Body
                            <textarea name="sections[{{ $section->id }}][body]" rows="4">{{ old('sections.'.$section->id.'.body', $section->body) }}</textarea>
                            <small>Separate paragraphs with a blank line. Basic formatting is sanitized before display.</small>
                        </label>

                        @include('admin.components.media-field', [
                            'name' => 'sections['.$section->id.'][image]',
                            'oldName' => 'sections.'.$section->id.'.image',
                            'label' => 'Section image',
                            'value' => $section->image,
                            'media' => $media,
                        ])

                        <div class="form-row">
                            <label>CTA label
                                <input name="sections[{{ $section->id }}][cta_label]" value="{{ old('sections.'.$section->id.'.cta_label', $section->cta_label) }}">
                            </label>
                            <label>CTA URL
                                <input name="sections[{{ $section->id }}][cta_url]" value="{{ old('sections.'.$section->id.'.cta_url', $section->cta_url) }}">
                            </label>
                        </div>

                        @if(array_key_exists('list', $sectionPayload))
                            <label>List items
                                <textarea name="sections[{{ $section->id }}][list]" rows="4">{{ old('sections.'.$section->id.'.list', implode("\n", $sectionPayload['list'] ?? [])) }}</textarea>
                                <small>One item per line.</small>
                            </label>
                        @endif

                        @if(array_key_exists('callout', $sectionPayload))
                            <label>Callout
                                <textarea name="sections[{{ $section->id }}][callout]" rows="2">{{ old('sections.'.$section->id.'.callout', $sectionPayload['callout'] ?? '') }}</textarea>
                            </label>
                        @endif

                        @if(array_key_exists('steps', $sectionPayload))
                            <label>Steps
                                <textarea name="sections[{{ $section->id }}][steps]" rows="7">{{ old('sections.'.$section->id.'.steps', collect($sectionPayload['steps'] ?? [])->map(fn($step) => ($step['title'] ?? '').' | '.($step['body'] ?? ''))->implode("\n")) }}</textarea>
                                <small>One step per line using <code>Title | Description</code>. The visual step layout remains code-controlled.</small>
                            </label>
                        @endif

                        <label class="checkbox-field">
                            <input type="checkbox" name="sections[{{ $section->id }}][is_enabled]" value="1" @checked(old('sections.'.$section->id.'.is_enabled', $section->is_enabled))>
                            Section enabled
                        </label>
                    </div>
                @endforeach
            </fieldset>
        @endif

        @if($page->exists)
            <fieldset class="form-section">
                <legend>Add structured section</legend>
                <p class="field-help">Choose a registered section type. Arbitrary layout or CSS is never stored in page content.</p>
                <div class="form-row">
                    <label>Key
                        <input name="new_section_key" value="{{ old('new_section_key') }}" placeholder="e.g. support-cta">
                    </label>
                    <label>Type
                        <select name="new_section_type">
                            @foreach(['rich_text', 'split_content', 'cta', 'quote', 'feature_grid', 'contact_block', 'faq', 'featured_topics', 'featured_courses', 'featured_products', 'latest_journal', 'framework'] as $type)
                                <option value="{{ $type }}">{{ str_replace('_', ' ', ucfirst($type)) }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <div class="form-row">
                    <label>Heading
                        <input name="new_section_heading" value="{{ old('new_section_heading') }}">
                    </label>
                    <label>Order
                        <input type="number" min="0" name="new_section_sort_order" value="{{ old('new_section_sort_order', 0) }}">
                    </label>
                </div>
                <label>Body
                    <textarea name="new_section_body" rows="3">{{ old('new_section_body') }}</textarea>
                </label>
                @include('admin.components.media-field', ['name' => 'new_section_image', 'label' => 'Section image', 'value' => old('new_section_image'), 'media' => $media])
                <div class="form-row">
                    <label>CTA label
                        <input name="new_section_cta_label" value="{{ old('new_section_cta_label') }}">
                    </label>
                    <label>CTA URL
                        <input name="new_section_cta_url" value="{{ old('new_section_cta_url') }}">
                    </label>
                </div>
                <label class="checkbox-field">
                    <input type="checkbox" name="new_section_enabled" value="1" checked>
                    Section enabled
                </label>
            </fieldset>
        @endif

        <button class="button button-dark" type="submit">Save page <span aria-hidden="true">↗</span></button>
    </form>
@endsection
