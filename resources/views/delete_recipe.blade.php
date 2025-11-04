@php
// Partial/template for a delete confirmation. The index view uses a browser confirm for now,
// but this provides an HTML modal if you'd like to style a nicer confirmation dialog.
@endphp

<div id="delete-recipe-template" style="display:none">
    <div class="delete-modal">
        <p>Are you sure you want to delete this recipe?</p>
        <button id="confirm-delete">Yes, delete</button>
        <button id="cancel-delete">Cancel</button>
    </div>
</div>
