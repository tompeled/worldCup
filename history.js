function showPoints() {
    let players = document.querySelector(".apostas-e-acc").childElementCount - 2;
    for (let i = 1; i <= players; i++) {
        let total = 0;
        for (let j = 1; j < 65; j++) {
            let real1 = document.querySelector("#J" + j + "_1").textContent;
            let real2 = document.querySelector("#J" + j + "_2").textContent;
            if (real1 === "-" || real2 === "-")
                break;
            else {
                parseInt(real1);
                parseInt(real2);
            }
            let playerBet1 = parseInt(document.querySelector("#B" + i + "J" + j + "_1").textContent);
            let playerBet2 = parseInt(document.querySelector("#B" + i + "J" + j + "_2").textContent);
            console.log(playerBet1, playerBet2, real1, real2);
            let points = 0;
            let add1 = real1 - playerBet1;
            let add2 = real2 - playerBet2;
            points += Math.abs(add1) + Math.abs(add2);

            if ((playerBet1 > playerBet2 && real1 <= real2) || (playerBet1 < playerBet2 && real1 >= real2) || (playerBet1 === playerBet2 && real1 !== real2))
                points += 3;


            document.querySelector("#P" + i + "J" + j).textContent = "" + points;
            total += points;
            document.querySelector("#PT" + i + "J" + j).textContent = "" + total;
        }
    }
}

showPoints();