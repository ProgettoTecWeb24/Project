function showTab(tabId) {
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.add('hidden'));
    document.getElementById(tabId).classList.remove('hidden');

    const buttons = document.querySelectorAll('.tab');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}