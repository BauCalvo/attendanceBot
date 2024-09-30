function getTime2(hours, minutes) {
    // Convierto 2 campos, horas y minutos, a segundos desde las 00:00 del día actual
    const a = hours * 60 * 60;
    const b = minutes * 60;
    return a + b;
}

function getDay(timestamp) {
    // Agarro una timestamp y devuelvo el día en el que está
    const a = timestamp / 86400;
    console.log(a);
    // Lo formateo en Date
    const b = new Date(timestamp * 1000);
    console.log(b)
    // Agarro el 00:00 del día
    const c = Math.floor(a) * 86400;
    console.log(c)
    // Lo formateo en Date
    const d = new Date(c * 1000);
    console.log(d)
    // Agarro el 12:30 en segundos desde las 00:00
    const e = getTime2(12, 30);
    console.log(e)
    // Sumo el 12:30 a la fecha del día que arrancó desde 00:00
    const f = e + c;
    console.log(f)
    // Lo formateo en Date
    const g = new Date(e * 1000);
    console.log(g)
    // Me queda el día actual en el que está la timestamp pero en las 12:30 en vez de cualquier fecha en la que estaba
}

getDay(1717637797)

/* 
19880.06709490741
2024-06-06T01:36:37.000Z
1717632000
2024-06-06T00:00:00.000Z
45000
1717677000
1970-01-01T12:30:00.000Z
*/

