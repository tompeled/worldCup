<?php
include_once "header.php";
?>

    <section class="main-container">
        <div class="main-wrapper">
            <h2>Home</h2>
            <?php
            if (isset($_SESSION["u_id"])) {
                echo "You are logged in!";
            }
            ?>
            <h3>REGULAMENTO</h3>
            <p>
                Cada jogador faz uma aposta de 50 euros e tem direito a uma conta de acesso ao site.
            </p>
            <br>
            <p>
                Apostas:
            </p>
            <ul>
                <li>
                    A aposta inicial é feita nos jogos da 1.ª fase (fase de grupos) e na previsão dos 4 primeiros
                    classificados do torneio. Esta aposta deve ser submetida até ao dia 12 de Junho, pelas 19h00.
                </li>
                <li>
                    As apostas para os 8ºos final deverão ser enviadas até ao dia 29 de Junho até às 19 horas.
                </li>
                <li>
                    As apostas para os 4ºos final deverão ser enviadas até ao dia 5 de Julho até às 19 horas.
                </li>
                <li>
                    As apostas para as meias-finais deverão ser enviadas até ao dia 9 de Julho até às 19 horas.
                </li>
                <li>
                    As apostas para a final e terceiro classificado deverão ser enviadas até ao dia 13 de Julho até
                    às 19 horas.
                </li>
            </ul>
            <br>
            <p>
                O vencedor será aquele que acumular menos pontos pelas seguintes regras:
            </p>
            <ul>
                <li>
                    Por cada aposta errada na equipa vencedora / empate em cada jogo: 3 pontos (aplicável a todos os
                    jogos)
                </li>
                <li>
                    Por cada golo de diferença na aposta dos golos de cada uma das equipas: 1 ponto (aplicável a
                    todos os
                    jogos)
                </li>
            </ul>
            <table>
                <tr>
                    <th>APOSTA</th>
                    <th>RESULTADO REAL</th>
                    <th>PONTOS</th>
                    <th>EXPLICAÇÂO</th>
                </tr>
                <tr>
                    <td>Suiça 2 Togo 1</td>
                    <td>Suiça 3 Togo 0</td>
                    <td>2</td>
                    <td>Resultado certo pois Suiça ganhou, 2 golos errados 1 para a Suiça e 1 para o Togo, logo 2
                        pontos
                    </td>
                </tr>
                <tr>
                    <td>Suiça 2 Togo 1</td>
                    <td>Suiça 1 Togo 1</td>
                    <td>4</td>
                    <td>Resultado errado pois Suiça não ganhou - 3 pontos; Golos de Togo certos mas 1 golo errado na
                        Suiça - mais 1 ponto; Total de penalização 4 pontos
                    </td>
                </tr>
            </table>
            <ul>
                <li>Por cada equipa que chegue às meias-finais e que não tenha sido escolhida pelo jogador na Aposta: 15
                    pontos
                </li>
                <li>Por cada equipa que tenha apostado como uma das 4 primeiras, mas no lugar errado: 5 pontos
                </li>
            </ul>
            <br>
            <p>Exemplo: Aposta nos 4 primeiros</p>
            <table>
                <tr>
                    <th>APOSTA</th>
                    <th>RESULTADO REAL</th>
                    <th>PONTOS</th>
                    <th>EXPLICAÇÂO</th>
                </tr>
                <tr>
                    <td>1.º Portugal</td>
                    <td>1.º Portugal</td>
                    <td>0</td>
                    <td>Certo - não tem pontos de penalização</td>
                </tr>
                <tr>
                    <td>2.º Brasil</td>
                    <td>2.º Togo</td>
                    <td>5</td>
                    <td>Brasil está certo nos 4 primeiros mas não no lugar certo</td>
                </tr>
                <tr>
                    <td>3.º França</td>
                    <td>3.º Angola</td>
                    <td>15</td>
                    <td>França não ficou nos 4 primeiros, logo penalização total</td>
                </tr>
                <tr>
                    <td>4.º Suiça</td>
                    <td>4.º Brasil</td>
                    <td>15</td>
                    <td>Suiça não ficou nos 4 primeiros, logo penalização total</td>
                </tr>
            </table>


        </div>

    </section>

<?php
include_once "footer.php";
?>