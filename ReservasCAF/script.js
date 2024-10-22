document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("reservationModal");
    const closeModal = document.querySelector(".close");

    function abrirModal(button) {
        const fecha = document.getElementById("fechaReserva").value;
        const horarioId = button.getAttribute("data-horario");

        if (!fecha) {
            alert("Por favor, selecciona una fecha primero.");
            return;
        }

        modal.style.display = "block";
        modal.dataset.fecha = fecha;
        modal.dataset.horario = horarioId;
    }

    closeModal.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }

    function confirmarReserva() {
        const runInput = document.getElementById("run").value;
        if (runInput.trim() === "") {
            alert("Por favor, ingrese un RUT válido.");
            return;
        }

        const fecha = modal.dataset.fecha;
        const horarioId = modal.dataset.horario;

        fetch('guardar_reserva.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                run: runInput,
                fecha: fecha,
                horario_id: horarioId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Recargar la página para mostrar la reserva
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    window.confirmarReserva = confirmarReserva;
    window.abrirModal = abrirModal;
});
