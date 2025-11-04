@php
// Partial/template for an edit recipe modal. The main page currently uses an inline modal, but
// we keep this file so other parts of the app can include or render a dedicated edit view.
@endphp

<div id="edit-recipe-template" style="display:none">
    <form id="edit-recipe-form">
        <h3>Edit Recipe</h3>
        <div>
            <label for="e-name">Name</label>
            <input id="e-name" name="name" />
        </div>
        <div>
            <label for="e-desc">Description</label>
            <textarea id="e-desc" name="description"></textarea>
        </div>
        <!-- Add additional fields as needed -->
    </form>
</div>
