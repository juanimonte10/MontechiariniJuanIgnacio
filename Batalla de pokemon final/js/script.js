//VARIABLES QUE VOY A UTILIZAR MAS ADELANTE PARA PODER GUARDAR LOS POKEMONES EN SUS DETERMINADOS EQUIPOS
let equipo1 = [];
let equipo2 = [];

//En lo siguiente voy intentare llamar todas las IDS de la API
async function obtenerpokemon(id) {
    const response = await fetch (`https://pokeapi.co/api/v2/pokemon/${id}`);
    //hago un if por si la id que ponga del pokemon no se encuentre o no existe 
    if(!response.ok) throw new Error(" La ID del pokemon no se ha encontrado o no existe") // la funcion throw new error va a interrumpir el codigo si llega a ver algun error 
    const data = await response.json();
    return {
        nombre: data.name,
        imagen: data.sprites.front_default,
        vida:  data.stats.find(stat=> stat.stat.name === "hp").base_stat,
        ataque: data.stats.find(stat=>stat.stat.name === "attack").base_stat,
        defensa: data.stats.find(stat=>stat.stat.name === "defense").base_stat
    }; // el find sirve para buscar un elemento que se encuentre dentro de un array que cumpla con la condicion solicitada, luego el base_stat va obtener el valor de la estadistica solicitada
}


//la funcion para poder lograr mostrar los pokemones de los equipos
function mostrarpoke(pokemon,lado){
    const contenedor=document.getElementById(`equipo${lado}`);
    const div= document.createElement("div");
    div.classList.add("pokemon");
    div.innerHTML = `
    <h3>${pokemon.nombre}</h3>
    <img src= "${pokemon.imagen}" alt= "${pokemon.nombre}">
    <p>vida: ${pokemon.vida}</p>
    <p>ataque: ${pokemon.ataque}</p>
    <p>defensa: ${pokemon.defensa}</p>
    `;
    contenedor.appendChild(div)// esta funcion nos ayudar agregar nuestro nuevo div al html original
}

// Aclaracion , esto es para poder cargarlo manualmente el codigo
//SI FUNCIONA , DEBERIA CARGAR LOS POKEMONES DEL PRIMER EQUIPO
document.getElementById("cargarequipo1").addEventListener("click", async function () {
const IDPOKE1= document.getElementById("epokemon1").value;
const IDPOKE2= document.getElementById("epokemon2").value;
const IDPOKE3= document.getElementById("epokemon3").value;

if (!IDPOKE1 || !IDPOKE2 || !IDPOKE3){
    alert ("ingersar las ids de los pokemones");
    return;
}
document.getElementById("equipo1").innerHTML="<h2> Equipo 1</h2>" // va a limpiar a la hora de cargar los pokemones
try {
    const pokemonE1= await obtenerpokemon (IDPOKE1);
    const pokemonE2= await obtenerpokemon (IDPOKE2);
    const pokemonE3= await obtenerpokemon (IDPOKE3);

    mostrarpoke (pokemonE1,1);
    mostrarpoke (pokemonE2,1);
    mostrarpoke (pokemonE3,1);

    equipo1=[pokemonE1,pokemonE2,pokemonE3];
} catch (error){
    alert("hubo problemas al obtener los pokemones")
}
})
//----------------- SEPARO PARA HACER LA PARTE DEL EQUIPO 2 --------------
document.getElementById("cargarequipo2").addEventListener("click", async function () {
    const ID2POKE1=document.getElementById("e2pokemon1").value;
    const ID2POKE2=document.getElementById("e2pokemon2").value;
    const ID2POKE3=document.getElementById("e2pokemon3").value;

    if(!ID2POKE1 || !ID2POKE2 || !ID2POKE3 ){
        alert(" ingresar las ids de los pokemones");
        return;
    }

    document.getElementById("equipo2").innerHTML=" <h2> Equipo 2 </h2>"

    try{
        const pokemonB1 = await obtenerpokemon (ID2POKE1);
        const pokemonB2 = await obtenerpokemon (ID2POKE2);
        const pokemonB3 = await obtenerpokemon (ID2POKE3);

        mostrarpoke (pokemonB1,2);
        mostrarpoke (pokemonB2,2);
        mostrarpoke (pokemonB3,2);

        equipo2 =[pokemonB1,pokemonB2,pokemonB3];
    } catch (error) {
        alert (" hubo problema al obtener los pokemones")
    }
})

// Aca la funcion para poder cargar aletoriamente los pokemones
function generarIDS(){
    return Math.floor(Math.random() *1024) + 1 ; // el random como dice , te devuelve un numero aleatorio y el floor sirve para redondear un numero (hablando de decimal , se hace para abajo)
}
async function cargaraleteriamente() {
    const IDS=[];
    while (IDS.length < 6){
        const AUX    = generarIDS();
        if(!IDS.includes(AUX)){
            IDS.push(AUX); // nos va ayudar acumular los pokemones , ( su funcion es agregar un elemento en el final de nuestro  array)
        }
    }

    const IDSEQUIPO1= IDS.slice(0,3) // el slice sirve para cortar una parte del array sin realizar modificaciones
    const IDSEQUIPO2 = IDS.slice(3);

    try{
        const pokes1= await Promise.all (IDSEQUIPO1.map(id=>obtenerpokemon(id)));// el promise me va ayudar a pedir los 3 pokemos de una
        const pokes2= await Promise.all (IDSEQUIPO2.map(id=>obtenerpokemon(id)));
        
        equipo1=pokes1;
        equipo2=pokes2;
        
        const contenedor1= document.getElementById("equipo1")
        const contenedor2= document.getElementById("equipo2");
        
        contenedor1.innerHTML="<h2> Equipo 1 </h2>"
        contenedor2.innerHTML="<h2> Equipo 2 </h2>"

        equipo1.forEach(p => mostrarpoke(p,1)); 
        equipo2.forEach(p => mostrarpoke(p,2)); 
    } catch(error){
        console.error (" Hubo un problema a cargar los equipos aleateoriamente" + error)
        alert(" Hubo un error ")
    }
}
document.getElementById("cargarale").addEventListener("click",() => {
    cargaraleteriamente();
});// Sirve para cargar los equipos aleatoriamente  ,   podria usar tambien un Domcontenloades que  es un evento especial (esta relacionado al Dom)


//------------------------------ FUNCION PARA COMPARAR LOS ATRIBUTOS -----------------------------------------------
document.getElementById("iniciarBatalla").addEventListener("click", function(){
    if(equipo1.length<3 || equipo2.length<3){
        alert(" Debes cargar los equipos primero")
        return;
    }

    const total1 = equipo1.reduce((acc, p) => acc + p.ataque + p.defensa, 0);
    const total2 = equipo2.reduce((acc, p) => acc + p.ataque + p.defensa, 0);

    let resultado = "";
    if (total1 > total2) {
        resultado = `¡Equipo 1 gana! (${total1} vs ${total2})`;
    } else if (total2 > total1) {
        resultado = `¡Equipo 2 gana! (${total2} vs ${total1})`;
    } else {
        resultado = `¡Empate! Ambos tienen ${total1} puntos.`;
    }

    document.getElementById("resultado").innerHTML=resultado;

});
// Aclaracion esto se vuelve utilizar en el dados.js ( se va a mostrar ahi , mas completo)

