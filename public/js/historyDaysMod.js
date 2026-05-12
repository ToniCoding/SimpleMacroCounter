const refreshHistoryBtn = document.getElementById('last-days-history-btn');

refreshHistoryBtn.addEventListener('click', () => {
    let numberOfDays = document.getElementById('lastDays');
    window.location =`/history?lastDays=${numberOfDays.value}`;
})
