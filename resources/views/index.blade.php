@extends('layouts.app')

@section('title', 'Recipe Manager')

@section('body')
	<div class="container">
		<div class="header">
			<div class="title">
				<div class="logo">
					<!-- chef hat icon -->
					<svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M12 3C9.243 3 7 5.243 7 8c0 .166.012.33.035.492C4.24 9.034 2 11.61 2 14.5 2 17.537 4.463 20 7.5 20h9c3.037 0 5.5-2.463 5.5-5.5 0-2.89-2.24-5.466-5.035-6.008.023-.162.035-.326.035-.492 0-2.757-2.243-5-5-5z" fill="white" opacity="0.95"/>
					</svg>
				</div>
				<div>
					<h1>Recipe Manager</h1>
					<p>Manage your favorite recipes</p>
				</div>
			</div>
			<button class="add-btn" id="open-add">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="opacity:0.95" xmlns="http://www.w3.org/2000/svg"><path d="M12 5v14M5 12h14" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				Add Recipe
			</button>
		</div>

		<div class="panel">
			<table>
				<thead>
					<tr>
						<th style="width:46%">Recipe Name</th>
						<th style="width:18%">Category</th>
						<th style="width:12%">Time</th>
						<th style="width:12%">Servings</th>
						<th style="width:12%">Actions</th>
					</tr>
				</thead>
				<tbody>
					<tr class="table-row">
						<td colspan="5" style="text-align:center;color:var(--muted);padding:46px 18px">No recipes found. Click "Add Recipe" to get started.</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
@endsection

@section('scripts')
<script>
	(function(){
		const createModal = () => {
			const html = `
			<div class="modal-backdrop" id="recipe-modal" role="dialog" aria-modal="true" aria-hidden="true">
				<div class="modal-card" role="document">
					<div class="modal-header">
						<div>
							<div class="modal-title" id="modal-title">Add New Recipe</div>
							<div class="modal-sub">Add a new recipe to your collection.</div>
						</div>
						<button class="btn-ghost" id="close-modal">✕</button>
					</div>
					<div class="modal-body">
						<div class="field">
							<label>Recipe Name</label>
							<input type="text" id="m-name" class="input" placeholder="e.g., Chocolate Chip Cookies">
						</div>
						<div class="field" style="margin-top:12px">
							<label>Description</label>
							<textarea id="m-desc" class="input" placeholder="Brief description of the recipe"></textarea>
						</div>

						<div class="form-row">
							<div class="field">
								<label>Category</label>
								<input id="m-category" class="input small-input" placeholder="e.g., Dessert, Italian">
							</div>
							<div class="field">
								<label>Servings</label>
								<input id="m-servings" class="input small-input" placeholder="4">
							</div>
						</div>

						<div class="form-row">
							<div class="field">
								<label>Prep Time (minutes)</label>
								<input id="m-prep" class="input small-input" placeholder="15">
							</div>
							<div class="field">
								<label>Cook Time (minutes)</label>
								<input id="m-cook" class="input small-input" placeholder="30">
							</div>
						</div>

						<div style="margin-top:12px">
							<label>Ingredients</label>
							<div style="display:flex;gap:10px;margin-top:8px">
								<input id="m-ingredient" class="input" placeholder="e.g., 2 cups flour">
								<button class="pill-btn" id="add-ingredient">+ Add</button>
							</div>
							<div class="ingredient-list" id="ingredients"></div>
						</div>

						<div style="margin-top:12px">
							<label>Instructions</label>
							<div style="display:flex;gap:10px;margin-top:8px">
								<input id="m-instruction" class="input" placeholder="Describe this step">
								<button class="pill-btn" id="add-instruction">+ Add</button>
							</div>
							<div class="instruction-list" id="instructions"></div>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn-ghost" id="cancel-modal">Cancel</button>
						<button class="pill-btn" id="primary-action">Add Recipe</button>
					</div>
				</div>
			</div>
			`;
			document.body.insertAdjacentHTML('beforeend', html);
		};

		createModal();
		const modal = document.getElementById('recipe-modal');
		const closeModalBtn = document.getElementById('close-modal');
		const cancelBtn = document.getElementById('cancel-modal');
		const primaryBtn = document.getElementById('primary-action');

		const openModal = (mode='add', row=null) => {
			document.getElementById('modal-title').textContent = mode === 'add' ? 'Add New Recipe' : 'Edit Recipe';
			primaryBtn.textContent = mode === 'add' ? 'Add Recipe' : 'Save changes';
			modal.classList.add('show');
			modal.setAttribute('aria-hidden','false');
			if(mode === 'edit' && row){
				// populate
				document.getElementById('m-name').value = row.querySelector('.recipe-name')?.textContent?.trim() || '';
				document.getElementById('m-desc').value = row.querySelector('.recipe-desc')?.textContent?.trim() || '';
				document.getElementById('m-category').value = row.querySelector('.badge')?.textContent?.trim() || '';
				const metaSpans = row.querySelectorAll('.meta span');
				if(metaSpans.length>1) document.getElementById('m-servings').value = metaSpans[1].textContent.trim();
			} else {
				clearModal();
			}
		};

		const closeModal = () => { modal.classList.remove('show'); modal.setAttribute('aria-hidden','true'); };
		const clearModal = () => {
			['m-name','m-desc','m-category','m-servings','m-prep','m-cook','m-ingredient','m-instruction'].forEach(id=>{ const el=document.getElementById(id); if(el) el.value=''; });
			document.getElementById('ingredients').innerHTML='';
			document.getElementById('instructions').innerHTML='';
		};

		// wire Add Recipe button
		document.getElementById('open-add').addEventListener('click', (e)=>{ e.preventDefault(); openModal('add'); });

		// delegate edit buttons (if any rows exist later)
		document.addEventListener('click', function(e){
			const edit = e.target.closest('.action-edit');
			if(edit){ e.preventDefault(); const row = edit.closest('tr'); openModal('edit', row); }
		});

		// add ingredient
		document.getElementById('add-ingredient').addEventListener('click', function(e){
			e.preventDefault(); const val = document.getElementById('m-ingredient').value.trim(); if(!val) return; const list=document.getElementById('ingredients'); const d=document.createElement('div'); d.className='chip'; d.textContent=val; list.appendChild(d); document.getElementById('m-ingredient').value='';
		});

		// add instruction
		document.getElementById('add-instruction').addEventListener('click', function(e){
			e.preventDefault(); const val = document.getElementById('m-instruction').value.trim(); if(!val) return; const list=document.getElementById('instructions'); const idx=list.children.length+1; const d=document.createElement('div'); d.className='chip'; d.textContent=idx+'. '+val; list.appendChild(d); document.getElementById('m-instruction').value='';
		});

		// primary action (add/save) — placeholder: will just close modal
		primaryBtn.addEventListener('click', function(e){ e.preventDefault(); // TODO: persist
			closeModal();
		});

		closeModalBtn.addEventListener('click', closeModal);
		cancelBtn.addEventListener('click', closeModal);
		modal.addEventListener('click', function(e){ if(e.target===modal) closeModal(); });
	})();
</script>
@endsection

