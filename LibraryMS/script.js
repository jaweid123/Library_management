// script.js
const API = 'api.php';

const TABLES = [
  'Library_Db','Faculty_Db','Student_Db','Library_Staff_Db',
  'Category_Db','Book_Details_Db','Warehouse_Db','Transactions_Db',
  'Issue_Details_Db','Return_Details_Db','Penalty_Db','Registration_Db'
];

const configCols = {
  'Library_Db':['Library_id','BranchName','Location','LibraryManager','TotalBooks','StaffCount','MemberCount','BooksIssued','Status','Description'],
  'Faculty_Db':['Faculty_id','Library_id','FullName','Rank','DOB','Email','Address','AccountStatus'],
  'Student_Db':['Student_id','Library_id','Faculty_id','FullName','Rank','DOB','Email','City','Address','ContactNumber'],
  'Library_Staff_Db':['Staff_id','Library_id','FirstName','LastName','Email','Position','HireDate','ShiftTime','UserName','Password','Status','Phone'],
  'Category_Db':['Category_id','CategoryName','Description'],
  'Book_Details_Db':['Book_id','Library_id','Category_id','PublisherName','AuthorName','BookName','Edition','PageCount','Description','CopyCount','Status'],
  'Warehouse_Db':['Storage_id','Library_id','Book_id','Location','ShelfNumber','Quantity','CurrentLoad','Status'],
  'Transactions_Db':['Transaction_id','Faculty_id','Student_id','Book_id','IssueDate','ReturnDate','IssueBy','ReceiveBy','DueDate','Status','Note'],
  'Issue_Details_Db':['Issue_id','Student_id','Book_id','Faculty_id','IssueBy','IssueDate','ReturnDate'],
  'Return_Details_Db':['Ret_id','Student_id','Book_id','ReceiveBy','IssueDate','ReturnDate','DueDate'],
  'Penalty_Db':['Penalty_id','Student_id','Return_id','Amount','PenaltyDate','PaidStatus','DueDays'],
  'Registration_Db':['ID','Student_id','UserName','Password','Description']
};

let currentTable = TABLES[0];
let currentUser = null;

// DOM elements
const menuToggle = () => document.getElementById('menuToggle');
const topnav = () => document.getElementById('topnav');
const row1 = () => document.getElementById('row1');
const row2 = () => document.getElementById('row2');
document.addEventListener('DOMContentLoaded', async () => {
  // build topnav in two rows (6 + 6)
  buildTopNav();

  // fill selector
  const sel = document.getElementById('tableSelector');
  TABLES.forEach(t => {
    const opt = document.createElement('option'); opt.value=t; opt.textContent=t; sel.appendChild(opt);
  });
  sel.onchange = ()=> selectTable(sel.value);

  // buttons
  document.getElementById('btnRefresh').onclick = ()=> loadData();
  document.getElementById('btnAdd').onclick = ()=> openModal('add');
  document.getElementById('cancelBtn').onclick = closeModal;
  document.getElementById('saveBtn').onclick = saveEntity;
  document.getElementById('globalSearch').oninput = filterTable;

  // login controls
  document.getElementById('btnShowLogin')?.addEventListener('click', ()=> showModal('loginModal'));
  document.getElementById('loginCancel')?.addEventListener('click', ()=> hideModal('loginModal'));
  document.getElementById('loginSubmit')?.addEventListener('click', loginSubmit);
  document.getElementById('btnLogout')?.addEventListener('click', logout);

  // users modal buttons
  document.getElementById('userCancelBtn')?.addEventListener('click', ()=> hideModal('usersModal'));
  document.getElementById('userSaveBtn')?.addEventListener('click', saveUser);

  // menu toggle (hamburger)
  document.getElementById('menuToggle')?.addEventListener('click', ()=>{
    topnav().classList.toggle('hidden');
  });

  // check session
  await refreshSession();

  // initial table
  selectTable(currentTable);
});

function buildTopNav(){
  const r1 = row1(); const r2 = row2();
  r1.innerHTML=''; r2.innerHTML='';
  TABLES.forEach((t,i) => {
    const btn = document.createElement('button'); btn.className='nav-btn'; btn.textContent=t;
    btn.onclick = ()=> { selectTable(t); document.getElementById('tableSelector').value = t; topnav().classList.add('hidden'); };
    if (i < 6) r1.appendChild(btn); else r2.appendChild(btn);
  });
}

async function refreshSession(){
  try {
    const res = await fetch(`${API}?action=session`, {credentials:'same-origin'});
    const j = await res.json();
    if (j.success) {
      currentUser = j.user;
      showWelcome(currentUser.username);
      // show manage users button only if admin
      showManageUsersControl(currentUser.role === 'admin');
    } else {
      currentUser = null;
      showWelcome(null);
      showManageUsersControl(false);
    }
  } catch (e) {
    console.error(e);
  }
}

function showWelcome(username){
  // update DOM: add welcome text or hide
  const brand = document.querySelector('.brand');
  if (!brand) return;
  // remove existing span if any
  const existing = brand.querySelector('.welcome-span');
  if (existing) existing.remove();
  if (username) {
    const span = document.createElement('span');
    span.className = 'welcome-span';
    span.style.marginLeft = '12px';
    span.style.fontWeight = '600';
    span.textContent = `Welcome, ${username} 👋`;
    brand.appendChild(span);
    // show logout button if exists
    const logoutBtn = document.getElementById('btnLogout');
    if (logoutBtn) logoutBtn.style.display = 'inline-block';
    const loginBtn = document.getElementById('btnShowLogin');
    if (loginBtn) loginBtn.style.display = 'none';
  } else {
    const logoutBtn = document.getElementById('btnLogout');
    if (logoutBtn) logoutBtn.style.display = 'none';
    const loginBtn = document.getElementById('btnShowLogin');
    if (loginBtn) loginBtn.style.display = 'inline-block';
  }
}

function showManageUsersControl(show){
  // create manage users button dynamically if admin
  let btn = document.getElementById('btnManageUsers');
  if (show) {
    if (!btn) {
      btn = document.createElement('button');
      btn.id = 'btnManageUsers';
      btn.className = 'btn-muted';
      btn.textContent = 'Manage Users';
      btn.addEventListener('click', ()=> openUsersModal());
      const headerDiv = document.querySelector('header > div:nth-child(2)') || document.querySelector('header');
      headerDiv.insertBefore(btn, headerDiv.firstChild);
    } else btn.style.display = 'inline-block';
  } else {
    if (btn) btn.style.display = 'none';
  }
}

// ---- Auth ----
async function loginSubmit(){
  const u = document.getElementById('loginUsername').value.trim();
  const p = document.getElementById('loginPassword').value;
  if (!u || !p) return alert('Enter username & password');
  const fd = new FormData(); fd.append('action','login'); fd.append('username',u); fd.append('password',p);
  const res = await fetch(API, {method:'POST', body: fd, credentials:'same-origin'});
  const j = await res.json();
  if (j.success) {
    currentUser = j.user;
    showWelcome(currentUser.username);
    showManageUsersControl(currentUser.role === 'admin');
    hideModal('loginModal');
    alert('Login successful');
  } else {
    alert(j.message || 'Login failed');
  }
}

async function logout(){
  const fd = new FormData(); fd.append('action','logout');
  const res = await fetch(API, {method:'POST', body: fd, credentials:'same-origin'});
  const j = await res.json();
  if (j.success) {
    currentUser = null; showWelcome(null); showManageUsersControl(false);
    alert('Logged out');
  } else {
    alert('Logout failed');
  }
}

// ---- Users management (admin) ----
async function openUsersModal(){
  if (!currentUser || currentUser.role !== 'admin') return alert('Admin only');
  await refreshUsersList();
  showModal('usersModal');
}
async function refreshUsersList(){
  try {
    const res = await fetch(`${API}?action=users_list`, {credentials:'same-origin'});
    const j = await res.json();
    const area = document.getElementById('usersListArea');
    area.innerHTML = '';
    if (!j.success) return area.innerHTML = `<p style="color:red">${j.message}</p>`;
    const list = j.data;
    if (!list.length) { area.innerHTML = '<p>No users</p>'; return; }
    const wrapper = document.createElement('div'); wrapper.style.display='grid'; wrapper.style.gap='8px';
    list.forEach(u=>{
      const row = document.createElement('div'); row.style.display='flex'; row.style.justifyContent='space-between'; row.style.alignItems='center';
      const left = document.createElement('div'); left.textContent = `${u.id} — ${u.username} [${u.role}]`;
      const right = document.createElement('div');
      const edit = document.createElement('button'); edit.textContent='Edit'; edit.className='btn-muted'; edit.onclick = ()=> fillUserForm(u);
      const del = document.createElement('button'); del.textContent='Delete'; del.className='btn-danger'; del.onclick = ()=> deleteUser(u.id);
      right.appendChild(edit); right.appendChild(del);
      row.appendChild(left); row.appendChild(right); wrapper.appendChild(row);
    });
    area.appendChild(wrapper);
  } catch (e) { area.innerHTML = '<p>Error loading users</p>'; console.error(e); }
}
function fillUserForm(u){
  document.getElementById('userId').value = u.id;
  document.getElementById('userNameField').value = u.username;
  document.getElementById('userPasswordField').value = '';
  document.getElementById('userRoleField').value = u.role || 'user';
}
function clearUserForm(){ document.getElementById('userId').value=''; document.getElementById('userNameField').value=''; document.getElementById('userPasswordField').value=''; document.getElementById('userRoleField').value='user'; }

async function saveUser(){
  const id = document.getElementById('userId').value;
  const username = document.getElementById('userNameField').value.trim();
  const password = document.getElementById('userPasswordField').value;
  const role = document.getElementById('userRoleField').value || 'user';
  if (!username) return alert('Username required');
  if (id) {
    const fd = new FormData(); fd.append('action','users_update'); fd.append('id', id); fd.append('username', username); fd.append('role', role);
    if (password) fd.append('password', password);
    const res = await fetch(API, {method:'POST', body:fd, credentials:'same-origin'}); const j = await res.json();
    alert(j.message || (j.success? 'Updated':'Error')); if (j.success) { clearUserForm(); await refreshUsersList(); }
  } else {
    if (!password) return alert('Password required for new user');
    const fd = new FormData(); fd.append('action','users_add'); fd.append('username', username); fd.append('password', password); fd.append('role', role);
    const res = await fetch(API, {method:'POST', body:fd, credentials:'same-origin'}); const j = await res.json();
    alert(j.message || (j.success? 'Added':'Error')); if (j.success) { clearUserForm(); await refreshUsersList(); }
  }
}
async function deleteUser(id){
  if (!confirm('Delete user?')) return;
  const fd = new FormData(); fd.append('action','users_delete'); fd.append('id', id);
  const res = await fetch(API, {method:'POST', body: fd, credentials:'same-origin'}); const j = await res.json();
  alert(j.message || (j.success? 'Deleted':'Error')); if (j.success) await refreshUsersList();
}

// ---- Table & CRUD ----
function selectTable(t){
  currentTable = t;
  document.getElementById('modalTitle').textContent = t;
  document.getElementById('tableSelector').value = t;
  loadData();
}

async function loadData(){
  showFeedback('Loading...');
  if (!currentTable) return;
  try {
    const res = await fetch(`${API}?action=list&table=${encodeURIComponent(currentTable)}`, {credentials:'same-origin'});
    const data = await res.json();
    if (!data.success) { showFeedback('Load error: '+(data.message||'')); return; }
    renderTable(data.data);
    showFeedback('Loaded: '+(data.data.length||0)+' rows');
  } catch (e) { showFeedback('Load failed'); console.error(e); }
}

function renderTable(rows){
  const head = document.getElementById('tableHead'); const body = document.getElementById('tableBody');
  head.innerHTML = ''; body.innerHTML = '';
  const cols = configCols[currentTable] || (rows[0] ? Object.keys(rows[0]) : []);
  const trh = document.createElement('tr');
  cols.forEach(c => { const th = document.createElement('th'); th.textContent = c; trh.appendChild(th); });
  const thAction = document.createElement('th'); thAction.textContent = 'Actions'; trh.appendChild(thAction);
  head.appendChild(trh);
  rows.forEach(r => {
    const tr = document.createElement('tr');
    cols.forEach(c => { const td = document.createElement('td'); td.textContent = r[c] ?? ''; tr.appendChild(td); });
    const tdAct = document.createElement('td');
    const btnEdit = document.createElement('button'); btnEdit.textContent='Edit'; btnEdit.className='btn-muted'; btnEdit.onclick = ()=> openModal('edit', r);
    const btnDel = document.createElement('button'); btnDel.textContent='Delete'; btnDel.className='btn-danger'; btnDel.onclick = ()=> deleteEntity(r);
    tdAct.appendChild(btnEdit); tdAct.appendChild(btnDel);
    tr.appendChild(tdAct); body.appendChild(tr);
  });
}

function openModal(mode='add', row=null){
  if (!currentTable) return alert('Select a table first');
  const modalEl = document.getElementById('modal'); const form = document.getElementById('entityForm'); form.innerHTML=''; form.dataset.mode = mode;
  const cols = configCols[currentTable];
  cols.forEach(c=>{
    const wrapper = document.createElement('div'); wrapper.className='form-row';
    const label = document.createElement('label'); label.textContent = c;
    let input;
    if (mode==='edit' && c === cols[0]) {
      input = document.createElement('input'); input.type='text'; input.name=c; input.value = row[c] ?? ''; input.readOnly=true;
    } else {
      if (c.toLowerCase().includes('date')) input = document.createElement('input'), input.type='date';
      else if (/(amount|count|id|number|quantity|page)/i.test(c)) input = document.createElement('input'), input.type='number';
      else input = document.createElement('input'), input.type='text';
      input.name = c; input.value = row ? (row[c] ?? '') : '';
    }
    wrapper.appendChild(label); wrapper.appendChild(input); form.appendChild(wrapper);
  });
  showModal('modal');
}

function closeModal(){ hideModal('modal'); }

async function saveEntity(){
  const form = document.getElementById('entityForm');
  const mode = form.dataset.mode;
  const fd = new FormData();
  fd.append('action', mode === 'add' ? 'add' : 'update');
  fd.append('table', currentTable);
  const cols = configCols[currentTable] || [];
  cols.forEach(c => {
    const el = form.querySelector(`[name="${c}"]`);
    if (el) fd.append(c, el.value);
  });
  const res = await fetch(API, {method:'POST', body: fd, credentials:'same-origin'});
  const j = await res.json();
  alert(j.message || (j.success ? 'OK' : 'Error'));
  if (j.success) { closeModal(); loadData(); }
}

async function deleteEntity(row){
  const cols = configCols[currentTable] || [];
  const idcol = cols[0];
  if (!confirm('Are you sure to delete ID=' + row[idcol] + ' ?')) return;
  const fd = new FormData(); fd.append('action','delete'); fd.append('table', currentTable); fd.append(idcol, row[idcol]);
  const res = await fetch(API, {method:'POST', body: fd, credentials:'same-origin'});
  const j = await res.json();
  alert(j.message || (j.success ? 'Deleted' : 'Error'));
  if (j.success) loadData();
}

function filterTable(ev){
  const q = ev.target.value.toLowerCase();
  document.querySelectorAll('#tableBody tr').forEach(tr=>{ tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none'; });
}

function showFeedback(msg){ document.getElementById('feedback').textContent = msg; }
function showModal(id){ document.getElementById(id).classList.remove('hidden'); }
function hideModal(id){ document.getElementById(id).classList.add('hidden'); }

