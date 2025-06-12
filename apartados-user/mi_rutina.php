<?php
session_start();
include '../config.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}

$dni = $_SESSION['usuario'];


$query = "SELECT dia, ejercicio,series, repeticiones, peso
          FROM vista_rutina 
          WHERE dni = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $dni);
$stmt->execute();
$result = $stmt->get_result();

$ejercicios = [];
while ($row = $result->fetch_assoc()) {
    $ejercicios[$row['dia']][] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Rutina</title>
    <link rel="stylesheet" href="../styleUsuario.css">
    <link rel="stylesheet" href="../styleRutina.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<header>
   <a class="volver-btn" href="../InicioUsuario.php">Volver</a>
   <h1 class="welcome-title">MI RUTINA</h1>
   <a href="../logout.php" class="logout-btn">Cerrar sesión</a>
</header>

<div class="rutina-container" id="rutinaContainer">
    <?php
    $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    foreach ($dias as $dia):
    ?>
    <div class="dia" data-dia="<?= $dia ?>">
        <div class="acciones">
            <button onclick="abrirModal('<?= $dia ?>')">+</button>
            <button onclick="borrarEjercicio('<?= $dia ?>')">-</button>
        </div>
        <h3><?= $dia ?></h3>
        <div class="lista-ejercicios">
            <?php
            if (isset($ejercicios[$dia])):
                foreach ($ejercicios[$dia] as $e): ?>
                <div class="ejercicio" onclick="seleccionar(this)">
                    <?= htmlspecialchars($e['ejercicio']) ?><br>
                    <?= htmlspecialchars($e['series']) . 'x' . htmlspecialchars($e['repeticiones']) ?><br>
                    <?= htmlspecialchars($e['peso']) ?>Kg
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="modal" id="modalEjercicio">
    <div class="modal-content">
        <p id="pregunta">¿Nombre del ejercicio?</p>
        <input type="text" id="respuesta">
        <div class="modal-buttons">
            <button onclick="pasoAnterior()">Atrás</button>
            <button onclick="siguientePaso()">Siguiente</button>
        </div>
    </div>
</div>

<script>
    let paso = 0;
    let ejercicio = { nombre: '', series: '', repeticiones: '', peso: '', dia: '' };
    let ejercicioSeleccionado = null;

    function seleccionar(el) {
        document.querySelectorAll('.ejercicio').forEach(e => e.classList.remove('selected'));
        el.classList.add('selected');
        ejercicioSeleccionado = el;
    }

    function abrirModal(dia) {
        ejercicio = { nombre: '', series: '', repeticiones: '', peso: '', dia: dia };
        paso = 1;
        document.getElementById("pregunta").innerText = "¿Nombre del ejercicio?";
        document.getElementById("respuesta").value = "";
        document.getElementById("modalEjercicio").style.display = "flex";
    }

    function cerrarModal() {
        document.getElementById("modalEjercicio").style.display = "none";
    }

    function siguientePaso() {
        let r = document.getElementById("respuesta").value.trim();
        if (r === "") return;

        if (paso === 1) {
            ejercicio.nombre = r;
            document.getElementById("pregunta").innerText = "¿Cuántas series y repeticiones? (ej: 3x12)";
            document.getElementById("respuesta").value = "";
            paso = 2;
        } else if (paso === 2) {
            const parts = r.split('x');
            if (parts.length !== 2) return;
            ejercicio.series = parts[0];
            ejercicio.repeticiones = parts[1];
            document.getElementById("pregunta").innerText = "¿Cuántos Kg?";
            document.getElementById("respuesta").value = "";
            paso = 3;
        } else if (paso === 3) {
            ejercicio.peso = r;
            agregarEjercicio();
            cerrarModal();
        }
    }

    function pasoAnterior() {
        if (paso === 1) {
            cerrarModal();
            return;
        }

        paso--;
        if (paso === 1) {
            document.getElementById("pregunta").innerText = "¿Nombre del ejercicio?";
            document.getElementById("respuesta").value = ejercicio.nombre;
        } else if (paso === 2) {
            document.getElementById("pregunta").innerText = "¿Cuántas series y repeticiones? (ej: 3x12)";
            document.getElementById("respuesta").value = ejercicio.series + "x" + ejercicio.repeticiones;
        }
    }

    function agregarEjercicio() {
        const columna = [...document.querySelectorAll('.dia')].find(c => c.dataset.dia === ejercicio.dia);
        const contenedor = columna.querySelector('.lista-ejercicios');

        const div = document.createElement('div');
        div.className = 'ejercicio';
        div.innerHTML = `${ejercicio.nombre}<br>${ejercicio.series}x${ejercicio.repeticiones}<br>${ejercicio.peso}Kg`;
        div.onclick = () => seleccionar(div);
        contenedor.appendChild(div);

        fetch('EliminarGuardarRT.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                modo: 'guardar',
                dia: ejercicio.dia,
                nombre: ejercicio.nombre,
                series: parseInt(ejercicio.series),
                repeticiones: parseInt(ejercicio.repeticiones),
                peso: parseFloat(ejercicio.peso)
            })
        }).then(res => {
            if (!res.ok) {
                alert("Error al guardar el ejercicio");
            }
        }).catch(err => {
            alert("Error de conexión con el servidor");
            console.error(err);
        });
    }

    function borrarEjercicio(dia) {
        if (!ejercicioSeleccionado) {
            alert("Selecciona un ejercicio que quieras borrar.");
            return;
        }

        const confirmar = confirm("¿Seguro que deseas borrar este ejercicio?");
        if (!confirmar) return;

        const partes = ejercicioSeleccionado.innerHTML.split('<br>');
        const nombre = partes[0].trim();
        const [series, repeticiones] = partes[1].replaceAll(' ', '').split('x');
        const peso = partes[2].replace('Kg', '').trim();

        fetch('EliminarGuardarRT.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                modo: 'eliminar',
                dia: dia,
                nombre: nombre,
                series: parseInt(series),
                repeticiones: parseInt(repeticiones),
                peso: parseFloat(peso)
            })
        }).then(res => {
            if (!res.ok) {
                alert("Error al eliminar el ejercicio de la base de datos");
                return;
            }

            ejercicioSeleccionado.remove();
            ejercicioSeleccionado = null;
        }).catch(err => {
            alert("Error de conexión con el servidor");
            console.error(err);
        });
    }
</script>

</body>
</html>
