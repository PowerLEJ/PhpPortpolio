function showTab(tabName, element) {

    const navItems = document.querySelectorAll('.book-nav-item');
    navItems.forEach(item => item.classList.remove('active'));
    element.parentElement.classList.add('active');
    
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(tabContent => tabContent.style.display = 'none');
    
    const selectedTab = document.getElementById(tabName);
    if (selectedTab) {
        selectedTab.style.display = 'block';
    }
}

function toggleAllCheckboxes(source) {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = source.checked;
    });
}

function submitDeleteForm() {
    const form = document.getElementById('deleteForm');
    const checkboxes = document.querySelectorAll('input[name="selected_ids[]"]:checked');

    if (checkboxes.length === 0) {
        alert('삭제할 항목을 선택하세요.');
        return;
    }

    checkboxes.forEach((checkbox) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });

    if (confirm('선택된 항목을 삭제하시겠습니까?')) {
        form.submit();
    }
}