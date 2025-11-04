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
					@isset($recipes)
						@forelse($recipes as $recipe)
							<tr data-id="{{ $recipe->id }}" data-ingredients='@json($recipe->ingredients)' data-instructions='@json($recipe->instructions)'>
							<td>
								<div style="display:flex;flex-direction:column">
									<div class="recipe-name" style="font-weight:600">{{ $recipe->name }}</div>
									<div class="recipe-desc" style="color:var(--muted);font-size:13px">{{ $recipe->description }}</div>
								</div>
							</td>
							<td><span class="badge">{{ $recipe->category }}</span></td>
							<td class="meta"><span>{{ $recipe->prep ?? '' }} / {{ $recipe->cook ?? '' }}</span><span style="display:none">{{ $recipe->servings ?? '' }}</span></td>
							<td><span>{{ $recipe->servings ?? '' }}</span></td>
							<td>
								<button class="action-edit">Edit</button>
								<button class="action-delete">Delete</button>
							</td>
						</tr>
						@empty
						<tr class="table-row">
							<td colspan="5" style="text-align:center;color:var(--muted);padding:46px 18px">No recipes found. Click "Add Recipe" to get started.</td>
						</tr>
						@endforelse
					@else
						<tr class="table-row">
							<td colspan="5" style="text-align:center;color:var(--muted);padding:46px 18px">No recipes found. Click "Add Recipe" to get started.</td>
						</tr>
					@endisset
				</tbody>
			</table>
		</div>
	</div>
@endsection

@section('scripts')
<script>
	(function(){
	let currentMode = 'add';
	let editingId = null;

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
				// fetch the full recipe from server (uses data-id if present)
				editingId = row.dataset.id || null;
				if (!editingId) {
					// fallback to DOM values
					document.getElementById('m-name').value = row.querySelector('.recipe-name')?.textContent?.trim() || '';
					document.getElementById('m-desc').value = row.querySelector('.recipe-desc')?.textContent?.trim() || '';
					document.getElementById('m-category').value = row.querySelector('.badge')?.textContent?.trim() || '';
					const metaSpans = row.querySelectorAll('.meta span');
					if(metaSpans.length>1) document.getElementById('m-servings').value = metaSpans[1].textContent.trim();
				} else {
					fetch('/recipes/' + editingId).then(r => r.json()).then(data => {
						document.getElementById('m-name').value = data.name || '';
						document.getElementById('m-desc').value = data.description || '';
						document.getElementById('m-category').value = data.category || '';
						document.getElementById('m-servings').value = data.servings || '';
						document.getElementById('m-prep').value = data.prep ?? '';
						document.getElementById('m-cook').value = data.cook ?? '';
						// populate ingredients and instructions lists
						const ingrList = document.getElementById('ingredients');
						const instrList = document.getElementById('instructions');
						ingrList.innerHTML = '';
						instrList.innerHTML = '';
						(data.ingredients || []).forEach(i => { const d = document.createElement('div'); d.className='chip'; d.textContent = i; ingrList.appendChild(d); });
						(data.instructions || []).forEach((ins, idx) => { const d = document.createElement('div'); d.className='chip'; d.textContent = (idx+1)+'. '+ins; instrList.appendChild(d); });
					}).catch(err => {
						console.error(err);
						// fallback to clearing
						clearModal();
					});
				}
			} else {
				editingId = null;
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

		// delegate edit buttons (opens modal and loads recipe)
		document.addEventListener('click', function(e){
			const edit = e.target.closest('.action-edit');
			if(edit){ e.preventDefault(); const row = edit.closest('tr'); currentMode = 'edit'; openModal('edit', row); }
		});

		// delegate delete buttons (confirm then call API)
		document.addEventListener('click', function(e){
			const del = e.target.closest('.action-delete');
			if(del){
				e.preventDefault();
				const row = del.closest('tr');
				const id = row?.dataset?.id;
				if(!id){
					if(!confirm('Delete this recipe?')) return;
					row.remove();
					return;
				}
				if(!confirm('Delete this recipe?')) return;
				const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
				fetch('/recipes/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token || '' } }).then(r => {
					if(r.ok) {
						row.remove();
						// if no rows left, show placeholder
						const tbody = document.querySelector('.panel table tbody');
						if(!tbody.querySelector('tr')){
							tbody.innerHTML = '<tr class="table-row"><td colspan="5" style="text-align:center;color:var(--muted);padding:46px 18px">No recipes found. Click "Add Recipe" to get started.</td></tr>';
						}
					} else {
						alert('Failed to delete recipe');
					}
				}).catch(err => { console.error(err); alert('Failed to delete recipe'); });
			}
		});

		// add ingredient
		document.getElementById('add-ingredient').addEventListener('click', function(e){
			e.preventDefault(); const val = document.getElementById('m-ingredient').value.trim(); if(!val) return; const list=document.getElementById('ingredients'); const d=document.createElement('div'); d.className='chip'; d.textContent=val; list.appendChild(d); document.getElementById('m-ingredient').value='';
		});

		// add instruction
		document.getElementById('add-instruction').addEventListener('click', function(e){
			e.preventDefault(); const val = document.getElementById('m-instruction').value.trim(); if(!val) return; const list=document.getElementById('instructions'); const idx=list.children.length+1; const d=document.createElement('div'); d.className='chip'; d.textContent=idx+'. '+val; list.appendChild(d); document.getElementById('m-instruction').value='';
		});

		// primary action (add/save) — collect form data and POST to server
		primaryBtn.addEventListener('click', function(e){
			e.preventDefault();
			const name = document.getElementById('m-name').value.trim();
			if(!name){ alert('Please enter a recipe name'); return; }
			const payload = {
				name: name,
				description: document.getElementById('m-desc').value.trim(),
				category: document.getElementById('m-category').value.trim(),
				servings: document.getElementById('m-servings').value.trim(),
				prep: document.getElementById('m-prep').value.trim(),
				cook: document.getElementById('m-cook').value.trim(),
				ingredients: Array.from(document.getElementById('ingredients').children).map(c=>c.textContent.trim()),
				instructions: Array.from(document.getElementById('instructions').children).map(c=>c.textContent.replace(/^\d+\.\s*/, '').trim()),
			};

			const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

			// choose POST or PUT depending on mode
			const method = currentMode === 'edit' && editingId ? 'PUT' : 'POST';
			const url = currentMode === 'edit' && editingId ? '/recipes/' + editingId : '/recipes';

			fetch(url, {
				method: method,
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': token || ''
				},
				body: JSON.stringify(payload)
			}).then(r => r.json()).then(data => {
				// Update UI: replace "no recipes" row if present, and append new row
				const tbody = document.querySelector('.panel table tbody');
				// remove placeholder row if present
				const placeholder = tbody.querySelector('.table-row');
				if(placeholder && placeholder.querySelector('td[colspan]')){
					// if it's the "no recipes" placeholder, clear tbody
					tbody.innerHTML = '';
				}

				const tr = document.createElement('tr');
				tr.setAttribute('data-id', data.id || data.ID || '');
				if(data.ingredients) tr.setAttribute('data-ingredients', JSON.stringify(data.ingredients));
				if(data.instructions) tr.setAttribute('data-instructions', JSON.stringify(data.instructions));
				tr.innerHTML = `
					<td>
						<div style="display:flex;flex-direction:column">
							<div class="recipe-name" style="font-weight:600">${escapeHtml(data.name)}</div>
							<div class="recipe-desc" style="color:var(--muted);font-size:13px">${escapeHtml(data.description || '')}</div>
						</div>
					</td>
					<td><span class="badge">${escapeHtml(data.category || '')}</span></td>
					<td class="meta">${data.prep ?? ''} / ${data.cook ?? ''}</td>
					<td><span>${escapeHtml(data.servings || '')}</span></td>
					<td>
						<button class="action-edit">Edit</button>
						<button class="action-delete">Delete</button>
					</td>
				`;
				if(currentMode === 'edit' && editingId){
					// replace existing row
					const existing = tbody.querySelector('tr[data-id="'+editingId+'"]');
					if(existing) existing.replaceWith(tr);
					else tbody.appendChild(tr);
				} else {
					tbody.appendChild(tr);
				}

				// reset mode
				currentMode = 'add'; editingId = null;

				closeModal();
				clearModal();
			}).catch(err=>{
				console.error(err);
				alert('There was a problem saving the recipe.');
			});
		});

		// small helper to avoid XSS when inserting plain text
		function escapeHtml(s){
			return String(s || '').replace(/[&<>\"]/g, function(c){
				return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c];
			});
		}

		closeModalBtn.addEventListener('click', closeModal);
		cancelBtn.addEventListener('click', closeModal);
		modal.addEventListener('click', function(e){ if(e.target===modal) closeModal(); });
	})();
</script>
@endsection

