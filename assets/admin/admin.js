(function(){
  function addRow(tbodySel){
    const tbody = document.querySelector(tbodySel);
    if(!tbody) return;
    const i = tbody.querySelectorAll('tr').length;
    const tpl = `
      <tr>
        <td><input type="number" name="items[${i}][sort_order]" value="${i}"></td>
        <td><input type="text" name="items[${i}][label]" placeholder="Titre du lien"></td>
        <td><input type="url" name="items[${i}][url]" placeholder="https://..."></td>
        <td>
          <select name="items[${i}][type]">
            <option value="video">video</option>
            <option value="doc">doc</option>
            <option value="custom" selected>custom</option>
          </select>
        </td>
        <td><input type="text" name="items[${i}][icon]" placeholder="🎥"></td>
        <td>
          <select name="items[${i}][target]">
            <option value="_blank" selected>_blank</option>
            <option value="_self">_self</option>
          </select>
        </td>
        <td><button type="button" class="button helpy-remove-row">Supprimer</button></td>
      </tr>`;
    tbody.insertAdjacentHTML('beforeend', tpl);
  }

  document.addEventListener('click', function(e){
    const add = e.target.closest('[data-add-row]');
    if (add) {
      addRow(add.getAttribute('data-add-row'));
    }
    if (e.target.classList.contains('helpy-remove-row')) {
      e.target.closest('tr').remove();
    }
  });
})();
