<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fotos de Atletas</title>
    <link rel="stylesheet" href="style.css">
    <style>
        main { padding: 20px; }
        section {
            margin-bottom: 30px; background-color: #fff;
            padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333; border-bottom: 2px solid #ccc;
            padding-bottom: 10px; margin-bottom: 15px;
        }
        #upload-form label { display: block; margin-bottom: 5px; font-weight: bold; }
        #upload-form input[type="text"], #upload-form input[type="file"] {
            width: 100%; padding: 10px; margin-bottom: 10px;
            border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;
        }
        #upload-form button {
            background-color: black; color: white; border: 2px solid yellow;
            padding: 15px 25px; border-radius: 5px; cursor: pointer;
            font-size: 1em; transition: background-color 0.3s ease, color 0.3s ease;
        }
        #upload-form button:hover { background-color: #333; }
        #upload-message {
            margin-top: 10px; padding: 10px; border-radius: 4px; text-align: center;
        }
        #upload-message.success {
            background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;
        }
        #upload-message.error {
            background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;
        }
        .hidden { display: none; }
        .gallery {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;
        }
        .gallery-item {
            background-color: #f9f9f9; border: 1px solid #ddd;
            border-radius: 8px; padding: 10px; text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); position: relative;
        }
        .gallery-item img {
            max-width: 100%; height: 150px; object-fit: cover;
            border-radius: 4px; margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); transition: transform 0.3s ease-in-out;
        }
        .gallery-item img:hover { transform: scale(1.05); }
        .gallery-item h3 { margin: 0; font-size: 1.2em; color: #333; }
        .gallery-item p { margin: 5px 0; font-size: 0.9em; color: #666; }
        .delete-button {
            background-color: #dc3545; color: white; border: none;
            padding: 8px 12px; border-radius: 4px; cursor: pointer;
            font-size: 0.9em; margin-top: 10px;
        }
        .delete-button:hover { background-color: #c82333; }
        footer {
            background-color: #333; color: #fff; text-align: center;
            padding: 10px; position: relative; bottom: 0;
            width: 100%; margin-top: 30px;
        }
        
        @media (max-width: 600px) {
            header { padding: 15px; flex-direction: column; height: auto; }
            .header-right-content { flex-direction: column; gap: 5px; width: 100%; text-align: center; margin-top: 10px; }
            header h1 { font-size: 1.5rem; }
            h2 { font-size: 1.5em; }
            .gallery { grid-template-columns: 1fr; }
            .gallery-item img { height: 180px; }
            main { padding: 15px; }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-left-group">
            <a href="inicio.php" class="logo">
                <img src="img/Prosportes.png.png" alt="Logo Prosportes">
            </a>
            <h1>Fotos</h1> </div>
        
        <div class="header-buttons-group"> <?php
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

    <main>
        <section id="upload-area">
            <h2>Fotos: Compartilhe seus momentos</h2>
            <form id="upload-form" method="POST" enctype="multipart/form-data" action="upload_foto.php"> 
                <label for="nome">Nome do Atleta:</label>
                <input type="text" id="nome" name="nome" placeholder="Nome do atleta" required> 
                
                <label for="modalidade">Modalidade:</label>
                <input type="text" id="modalidade" name="modalidade" placeholder="Esporte" required> 
                
                <label for="foto">Selecione a foto:</label>
                <input type="file" id="foto" name="foto" accept="image/*" required>

                <button type="submit">
                    Enviar Foto
                </button>
            </form>
            <div id="upload-message" class="hidden"></div>
        </section>

        <section id="gallery-area">
            <h2>Fotos</h2>
            <div id="gallery" class="gallery">
                <p>Carregando fotos...</p>
            </div>
        </section>

        <section id="contratantes-area">
            <h2>Contratantes: Encontre o talento</h2>
            <p>Navegue pelas fotos e descubra atletas incríveis para suas oportunidades.</p>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Prosportes. Todos os direitos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function carregarFotosNaGaleria() {
            fetch('api/get_fotos.php')
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`Erro HTTP! Status: ${response.status}. Resposta: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(fotos => {
                    const galleryDiv = document.getElementById('gallery');
                    galleryDiv.innerHTML = '';

                    if (!Array.isArray(fotos) || fotos.length === 0) {
                        galleryDiv.innerHTML = '<p>Nenhuma foto encontrada na galeria.</p>';
                        return;
                    }

                    fotos.forEach(foto => {
                        const imgContainer = document.createElement('div');
                        imgContainer.classList.add('gallery-item');
                        imgContainer.dataset.fotoId = foto.id; 

                        const img = document.createElement('img');
                        img.src = foto.url;
                        img.alt = `${foto.nome} - ${foto.modalidade}`;

                        const captionName = document.createElement('h3');
                        captionName.textContent = foto.nome;

                        const captionModalidade = document.createElement('p');
                        captionModalidade.textContent = `Modalidade: ${foto.modalidade}`;

                        const deleteButton = document.createElement('button');
                        deleteButton.textContent = 'Apagar Foto';
                        deleteButton.classList.add('delete-button');
                        deleteButton.dataset.fotoId = foto.id;
                        deleteButton.dataset.fotoUrl = foto.url;

                        imgContainer.appendChild(img);
                        imgContainer.appendChild(captionName);
                        imgContainer.appendChild(captionModalidade);
                        imgContainer.appendChild(deleteButton);
                        galleryDiv.appendChild(imgContainer);
                    });
                })
                .catch(error => {
                    document.getElementById('gallery').innerHTML = '<p>Erro ao carregar fotos da galeria. Por favor, verifique o console do navegador para mais detalhes.</p>';
                    console.error('Erro ao carregar fotos:', error);
                });
        }

        function deletePhoto(photoId, photoUrl) {
            if (confirm('Tem certeza que deseja apagar esta foto? Esta ação é irreversível.')) {
                fetch('api/delete_foto.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: photoId, url: photoUrl })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`Erro HTTP! Status: ${response.status}. Resposta: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    const uploadMessage = document.getElementById('upload-message');
                    uploadMessage.textContent = data.message;
                    uploadMessage.classList.add(data.success ? 'success' : 'error');
                    uploadMessage.classList.remove('hidden');

                    setTimeout(() => {
                        uploadMessage.classList.add('hidden');
                    }, 3000);

                    if (data.success) {
                        carregarFotosNaGaleria();
                    }
                })
                .catch(error => {
                    const uploadMessage = document.getElementById('upload-message');
                    uploadMessage.textContent = 'Erro ao apagar a foto: ' + (error.message || 'Erro desconhecido.');
                    uploadMessage.classList.add('error');
                    uploadMessage.classList.remove('hidden');
                    setTimeout(() => {
                        uploadMessage.classList.add('hidden');
                    }, 3000);
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            carregarFotosNaGaleria();

            const uploadForm = document.getElementById('upload-form');
            if (uploadForm) {
                uploadForm.addEventListener('submit', function(event) {
                    event.preventDefault();

                    const nome = document.getElementById('nome').value;
                    const modalidade = document.getElementById('modalidade').value;
                    const fotoInput = document.getElementById('foto');
                    const uploadMessage = document.getElementById('upload-message');

                    uploadMessage.textContent = '';
                    uploadMessage.className = 'hidden';

                    if (fotoInput.files && fotoInput.files[0]) {
                        const formData = new FormData();
                        formData.append('nome', nome);
                        formData.append('modalidade', modalidade);
                        formData.append('foto', fotoInput.files[0]);

                        fetch('api/upload_foto.php', {
                            method: 'POST',
                            body: formData,
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(`Erro HTTP! Status: ${response.status}. Resposta: ${text}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            uploadMessage.textContent = data.message;
                            uploadMessage.classList.add(data.success ? 'success' : 'error');
                            uploadMessage.classList.remove('hidden');
                            
                            setTimeout(() => {
                                uploadMessage.classList.add('hidden');
                            }, 3000);

                            if (data.success) {
                                document.getElementById('upload-form').reset();
                                carregarFotosNaGaleria();
                            }
                        })
                        .catch(error => {
                            uploadMessage.textContent = 'Erro ao enviar a foto: ' + (error.message || 'Erro desconhecido.');
                            uploadMessage.classList.add('error');
                            uploadMessage.classList.remove('hidden');
                            setTimeout(() => {
                                uploadMessage.classList.add('hidden');
                            }, 3000);
                        });
                    } else {
                        uploadMessage.textContent = 'Por favor, selecione uma foto para enviar.';
                        uploadMessage.classList.add('error');
                        uploadMessage.classList.remove('hidden');
                        setTimeout(() => {
                            uploadMessage.classList.add('hidden');
                        }, 3000);
                    }
                });
            }

            document.getElementById('gallery').addEventListener('click', function(event) {
                if (event.target.classList.contains('delete-button')) {
                    const fotoId = event.target.dataset.fotoId;
                    const fotoUrl = event.target.dataset.fotoUrl;
                    deletePhoto(fotoId, fotoUrl);
                }
            });
        });
    </script>
</body>
</html>