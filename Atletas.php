<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atletas</title>
    <link href="style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: sans-serif;
            background-color: #f2f2f2;
            color: #333;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 30px;
            background-color: #000000;
            position: sticky;
            top: 0;
            z-index: 1000;
            height: 80px;
        }

        header .logo img {
            height: 65px;
            width: auto;
        }

        .header-left-group {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .header-left-group h1 {
            color: yellow;
            margin: 0;
            font-size: 1.8rem;
            white-space: nowrap;
        }

        .header-buttons-group {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .header-buttons-group button {
            background-color: black;
            color: white;
            border: 2px solid yellow;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
            white-space: nowrap;
        }
        .header-buttons-group button:hover {
            background-color: yellow;
            color: black;
            border-color: yellow;
        }

        .welcome-message {
            color: white;
            font-weight: bold;
            margin-right: 15px;
            white-space: nowrap;
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .navbar input {
            padding: 10px;
            width: 300px;
            border-radius: 20px 0 0 20px;
            border: 1px solid #ccc;
            outline: none;
            font-size: 1rem;
        }

        .navbar button {
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-left: none;
            background-color: #eee;
            border-radius: 0 20px 20px 0;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .navbar button:hover {
            background-color: #ddd;
        }

        .cards-container {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin-top: 40px;
            padding: 0 20px 50px;
            flex-wrap: wrap;
        }

        .card {
            background-color: #000;
            width: 280px;
            height: 350px;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            color: white;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .card-top {
            flex: 1;
            width: 100%;
            overflow: hidden;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        .card-top img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        .card-bottom {
            text-align: center;
            padding: 15px 10px;
            background-color: #333;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
        }

        .card-bottom p {
            margin: 5px 0;
            font-size: 1.1rem;
            font-weight: bold;
        }

        .card-bottom p.sport {
            font-size: 0.9rem;
            color: #ccc;
        }

        .message {
            text-align: center;
            margin-top: 30px;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .message.error {
            color: red;
        }

        .message.info {
            color: gray;
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                padding: 15px;
                height: auto;
            }
            .header-left-group {
                flex-direction: column;
                gap: 5px;
                text-align: center;
                width: 100%;
            }
            header h1 {
                font-size: 1.5rem;
            }
            .header-buttons-group {
                margin-top: 10px;
                flex-wrap: wrap;
                justify-content: center;
            }
            .navbar input {
                width: 70%;
            }
            .cards-container {
                flex-direction: column;
                align-items: center;
            }
            .card {
                width: 90%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-left-group">
            <a href="inicio.php" class="logo"> <img src="img/Prosportes.png.png" alt="Logo Prosportes">
            </a>
            <h1>Atletas</h1>
        </div>
        
        <div class="header-buttons-group">
            <?php
            if (isset($_SESSION['user_id'])) {
                $username = $_SESSION['username'] ?? 'Usuário';
                $user_id = $_SESSION['user_id'];
            ?>
                <span class="welcome-message">Bem-vindo, <?php echo htmlspecialchars($username); ?>!</span>
                <a href="Pagina_atleta.php?user_id=<?php echo htmlspecialchars($user_id); ?>">
                    <button>Meu Perfil</button>
                </a>
                <a href="logout.php">
                    <button>Sair</button>
                </a>
            <?php
            } else {
            ?>
                <a href="Cadastrar.php">
                    <button>Cadastrar</button>
                </a>
                <a href="Entrar.php">
                    <button>Entrar</button>
                </a>
            <?php
            }
            ?>
        </div>
    </header>

    <nav class="navbar">
        <input type="text" id="searchInput" placeholder="Pesquise seu atleta">
        <button id="searchBtn">🔍</button>
    </nav>

    <main class="cards-container" id="atletasContainer">
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const atletasContainer = document.getElementById('atletasContainer');
        let todosAtletas = [];

        function criarCardAtleta(atleta) {
            const cardLink = document.createElement('a');
            cardLink.style.textDecoration = 'none';
            cardLink.style.color = 'inherit';
            cardLink.href = `Pagina_atleta.php?user_id=${atleta.user_id}`;

            const card = document.createElement('div');
            card.classList.add('card');

            const cardTop = document.createElement('div');
            cardTop.classList.add('card-top');
            const img = document.createElement('img');
            img.src = atleta.foto_perfil_url || 'https://via.placeholder.com/280x200/000000/FFFFFF?text=Sem+Foto';
            img.alt = atleta.nome_completo;
            cardTop.appendChild(img);

            const cardBottom = document.createElement('div');
            cardBottom.classList.add('card-bottom');
            const pName = document.createElement('p');
            pName.classList.add('name');
            pName.textContent = atleta.nome_completo;
            const pSport = document.createElement('p');
            pSport.classList.add('sport');
            pSport.textContent = atleta.modalidade;
            cardBottom.appendChild(pName);
            cardBottom.appendChild(pSport);

            card.appendChild(cardTop);
            card.appendChild(cardBottom);
            cardLink.appendChild(card);

            return cardLink;
        }

        function carregarAtletas() {
            fetch('api/get_all_athletes.php')
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`Erro HTTP! Status: ${response.status}. Resposta: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    todosAtletas = data;
                    exibirAtletas(todosAtletas);
                })
                .catch(error => {
                    console.error('Erro ao carregar os atletas:', error);
                    atletasContainer.innerHTML = '<p class="message error">Erro ao carregar os atletas.</p>';
                });
        }

        function exibirAtletas(atletasParaExibir) {
            atletasContainer.innerHTML = '';
            if (atletasParaExibir.length === 0) {
                atletasContainer.innerHTML = '<p class="message info">Nenhum atleta encontrado.</p>';
                return;
            }
            atletasParaExibir.forEach(atleta => {
                const card = criarCardAtleta(atleta);
                atletasContainer.appendChild(card);
            });
        }

        document.getElementById('searchBtn').addEventListener('click', function () {
            const input = document.getElementById('searchInput').value.toLowerCase().trim();
            
            const atletasFiltrados = todosAtletas.filter(atleta => {
                const name = atleta.nome_completo.toLowerCase();
                const sport = atleta.modalidade.toLowerCase();
                return name.includes(input) || sport.includes(input);
            });
            
            exibirAtletas(atletasFiltrados);
        });

        document.getElementById('searchInput').addEventListener('keyup', function (event) {
            if (event.key === 'Enter') {
                document.getElementById('searchBtn').click();
            }
        });

        document.addEventListener('DOMContentLoaded', carregarAtletas);
    </script>
</body>
</html>