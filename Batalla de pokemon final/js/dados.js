//Variables que voy utilizar despues
let tiradasEquipo1 = [];
let tiradasEquipo2 = [];

// Función para tirar dos dados (1 a 6)
function tirarDosDados() {
    const dado1 = Math.floor(Math.random() * 6) + 1;
    const dado2 = Math.floor(Math.random() * 6) + 1;
    return {
        dados: [dado1, dado2],
        suma: dado1 + dado2
    };
}

// Actualizacion del botón a lo hora de iniciar batalla , si cada equipo hace los 3 tiros obligatorios
function verificacion() {
    const botonBatalla = document.getElementById("iniciarBatalla");
    if (tiradasEquipo1.length === 3 && tiradasEquipo2.length === 3) {
        botonBatalla.disabled = false; // el disabled es la funcion que nos permite deshabilitar los botones
    }
}

// funciones para lograr las tiradas de cada equipo
function tiradasequipos(equipo) {
    const tiradas = equipo === 1 ? tiradasEquipo1 : tiradasEquipo2;
    const boton = document.getElementById(`tirardados${equipo}`);
    const contenedor = document.getElementById(`dados${equipo}`);

    if (tiradas.length >= 3) return;

    const resultado = tirarDosDados();
    tiradas.push(resultado);

    // Mostrar resultado en pantalla
    const div = document.createElement("p");
    div.textContent = `Tirada ${tiradas.length}: ${resultado.dados[0]} + ${resultado.dados[1]} = ${resultado.suma}`;
    contenedor.appendChild(div);

    // Desactivar botón si ya tiró 3 veces
    if (tiradas.length === 3) {
        boton.disabled = true;
    }

    verificacion();
}

// Asignacion a los botones para que los dados "se lancen"
document.getElementById("tirardados1").addEventListener("click", () => tiradasequipos(1));
document.getElementById("tirardados2").addEventListener("click", () => tiradasequipos(2));

// Función para obtener el mayor puntaje
function obtenermayor(tiradas) {
    let mayor = 0;
    let nroTirada = 0;
    tiradas.forEach((tirada, i) => {
        if (tirada.suma > mayor) {
            mayor = tirada.suma;
            nroTirada = i + 1;
        }
    });
    return { mayor, nroTirada };
}

// Botón Iniciar Batalla
document.getElementById("iniciarBatalla").addEventListener("click", () => {
    if (equipo1.length < 3 || equipo2.length < 3) {
        alert(" Se debe cargar los equipos primero");
        return;
    }

    // los totales del ataque y defensa de los equipos
    const ataque1 = equipo1.reduce((acc, p) => acc + p.ataque, 0);
    const defensa1 = equipo1.reduce((acc, p) => acc + p.defensa, 0);
    const ataque2 = equipo2.reduce((acc, p) => acc + p.ataque, 0);
    const defensa2 = equipo2.reduce((acc, p) => acc + p.defensa, 0);

    const total1 = ataque1 + defensa1;
    const total2 = ataque2 + defensa2;

    let ganador = "";
    let desempate = false; // voy utilizar el desempate para cuando se de en caso , el empate de equipo

    if (total1 > total2) {
        ganador = " El equipo 1 gana  ";
    } else if (total2 > total1) {
        ganador = " El equipo 2 gana ";
    } else {
        // Solo hay empate si los totales son iguales
        ganador = "Hay empate . Se desempata atraves de los dados , con el puntaje mas alto";
        desempate = true;
    }

    // Si hay empate en totales, usá los dados
    if (desempate) {
        const mayor1 = obtenermayor(tiradasEquipo1);
        const mayor2 = obtenermayor(tiradasEquipo2);

        if (mayor1.mayor > mayor2.mayor) {
            ganador = ` Equipo 1 gana en los dados  `;
        } else if (mayor2.mayor > mayor1.mayor) {
            ganador = ` Equipo 2 gana en los dados  `;
        } else {
            ganador = ` Empate en los dados , tienen la misma cantidad `; // ${mayor1.mayor} ${mayor2.mayor} por si tengo que mostrar los resultados
        }

        ganador += `
        <br>Equipo 1 ; Mayor tirada: ${mayor1.mayor} (Tirada ${mayor1.nroTirada})
        <br>Equipo 2 ; Mayor tirada: ${mayor2.mayor} (Tirada ${mayor2.nroTirada})`;
    }

    // Tendria que mostrar los resultados completos
    document.getElementById("resultado").innerHTML = `
        <strong>${ganador}</strong> 
        <br>
        <br>
        <u>Totales:</u>
        <br>
        Equipo 1 ; Ataque: ${ataque1}, Defensa: ${defensa1} = ${total1}
        <br>
        Equipo 2 ; Ataque: ${ataque2}, Defensa: ${defensa2} = ${total2}
        <br>
        Diferencia entre el ataque del equipo 1 y defensa del equipo 2 : ${ataque1 - defensa2}
        <br>
        Diferencia entre el ataque del equipo 2 y defensa del equipo 1 : ${ataque2 - defensa1}
    `; // la parte de las diferencias podria no ir , tranquilamente 
});     // Utilizo un strong para resaltar en el html
     // esto lo tuve que aplicar para poder mostrar el texto completo en el final , lo pude aplicar tambien en la otra parte (script)