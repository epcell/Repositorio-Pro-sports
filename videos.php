<?php
session_start(); // Inicia a sessão para esta página
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vídeos</title>
    <link href="style.css" rel="stylesheet"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilos gerais */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: white;
            color: #000;
        }

        /* --- Header (Retângulo Preto do Topo) --- */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between; 
            background-color: #000000;
            padding: 8px 30px; 
            color: #fff;
            height: 80px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo img { 
            height: 65px; 
        }

        /* Novo contêiner para agrupar o menu e os botões */
        .menu-e-botoes {
            display: flex;
            align-items: center;
            gap: 20px; /* Ajuste este valor para controlar o espaço entre o menu e os botões */
            margin-left: auto; /* Empurra o grupo para a direita */
        }

        /* Estilos para o menu de navegação */
        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
            padding: 0; 
            margin: 0;
        }

        nav ul li a { 
            color: #fff; 
            text-decoration: none; 
            font-weight: bold; 
            transition: color 0.3s ease; 
        }

        nav ul li a:hover { 
            color: #ffd700; 
        }

        /* Estilos para os botões Cadastrar/Entrar/Sair */
        .header-buttons-group { 
            display: flex; 
            gap: 15px; 
            justify-content: center; 
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

        /* Estilos para o conteúdo principal */
        .video-content {
            padding: 40px 20px;
            text-align: center;
        }
        
        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            justify-content: center;
            max-width: 1200px;
            margin: 40px auto;
        }

        .video-item video {
            width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        @media (max-width: 768px) {
            header { 
                flex-direction: column; 
                height: auto; 
                padding: 15px; 
            }
            .menu-e-botoes {
                flex-direction: column;
                margin-left: 0;
                gap: 10px;
            }
            nav ul {
                flex-direction: column; 
                gap: 10px; 
                margin-top: 15px; 
                flex-wrap: wrap; 
                justify-content: center; 
            }
            .header-buttons-group { 
                margin-top: 15px; 
                flex-wrap: wrap; 
                justify-content: center; 
            }
            .welcome-message { 
                margin-right: 0; 
                margin-bottom: 10px; 
            }
        }
    </style>
</head>
<body>
    <header>
        <a href="inicio.php" class="logo">
            <img src="img/Prosportes.png.png" alt="Logo Prosportes">
        </a>
        <div class="menu-e-botoes">
            <nav>
                <ul>
                    <li><a href="inicio.php">Início</a></li>
                    <li><a href="Atletas.php">Atletas</a></li>
                    <li><a href="Fotos.php">Fotos</a></li>
                    <li><a href="Videos.php">Vídeos</a></li>
                </ul>
            </nav>
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
        </div>
    </header>

    <main class="video-content">
        <h1>Nossos Vídeos em Destaque</h1>
        <p>Assista aos melhores momentos dos nossos atletas.</p>

        <div class="video-grid">
            <div class="video-item">
                <video controls>
                    <source src="C:\xampp\htdocs\TCC\TCC\vdo" type="video/mp4">
                    Seu navegador não suporta a tag de vídeo.
                </video>
            </div>

            <div class="video-item">
                <video controls>
                    <source src="caminho_para_o_seu_video2.mp4" type="video/mp4">
                    Seu navegador não suporta a tag de vídeo.
                </video>
            </div>
            
            </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>