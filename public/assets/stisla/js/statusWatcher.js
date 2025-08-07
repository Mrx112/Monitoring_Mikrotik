let lastStatus = {};

function checkStatusUpdate() {
    fetch('/map/status')
        .then(res => res.json())
        .then(data => {
            data.forEach(d => {
                const prev = lastStatus[d.id];
                if (prev && prev === 'Hidup' && d.status === 'Mati') {
                    alert(`⚠️ Perangkat ${d.id} baru saja mati!`);
                }
                lastStatus[d.id] = d.status;
                const span = document.getElementById(`status-${d.id}`);
                if (span) {
                    span.className = d.status === 'Hidup' ? 'text-success' : 'text-danger';
                    span.textContent = d.status;
                }
            });
        });
}

setInterval(checkStatusUpdate, 5000);
