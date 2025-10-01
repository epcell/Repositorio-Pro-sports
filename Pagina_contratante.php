<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start();
    
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    if (!isset($_SESSION['user_id'])) {
        echo "Usuário não autenticado.";
        exit;
    }
    $user_id = $_SESSION['user_id'] ?? null;    

    echo "<script>console.log('user_id PHP: " . $user_id . "');</script>";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Contratante</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style1.css">
</head>    
<body>
    <div class="profile-container" id="profileContainer">
        <header class="page-header">
            <div class="header-left-group">
                <a href="#" class="logo">
                    <img src="img/Prosportes.png.png" alt="Logo">
                </a>
            </div>
            
            <a href="inicio_contratante.html">
                <button class="btn-voltar">
                    Voltar
                </button>
            </a>
        </header>

        <div class="profile-header">
            <div class="profile-picture-wrapper" id="profilePictureWrapper">
                <img id="profilePicture" class="profile-picture" src="img/branco.jpeg " alt="Foto de Perfil">
                <label for="profilePictureInput" class="profile-picture-edit-overlay">
                    <i class="fas fa-camera"></i>
                    <span>Mudar Foto</span>
                </label>
                <input type="file" id="profilePictureInput" accept="image/*">
            </div>
            <div class="profile-info">
                <div class="editable-field-group">
                    <h1 id="athleteName" class="view-mode-text">Carregando Nome...</h1>
                    <span class="edit-mode-input">
                        <input type="text" id="editAthleteName" value="" placeholder="Nome Completo do Contratante">
                    </span>
                </div>

                <div class="editable-field-group">
                    <p class="username"><span id="usernameText" class="view-mode-text">carregando_usuario</span></p>
                    <span class="edit-mode-input">
                        <input type="text" id="editUsername" value="" placeholder="nome_do_Contartante">
                    </span>
                </div>
                
                <div class="editable-field-group">
                    <p class="sport-details"><span id="sportText" class="view-mode-text">Carregando Modalidade...</span></p>
                    <span class="edit-mode-input">
                        <input type="text" id="editSport" value="" placeholder="Modalidade Esportiva">
                    </span>
                </div>

                <div class="editable-field-group">
                    <p class="bio"><span id="bioText" class="view-mode-text">Carregando biografia...</span></p>
                    <span class="edit-mode-input">
                        <textarea id="editBio" placeholder="Escreva uma breve descrição sobre o perfil." rows="4"></textarea>
                    </span>
                </div>

                <div class="contact-links">
                    <a href="mailto:exemplo@email.com" aria-label="Enviar e-mail" id="emailLink"><i class="fas fa-envelope"></i></a>
                    <a href="https://wa.me/55DDDXXXXXXXXX" target="_blank" aria-label="Fale no WhatsApp" id="whatsappLink"><i class="fab fa-whatsapp"></i></a>
                    <a href="https://www.instagram.com/" target="_blank" aria-label="Siga no Instagram" id="instagramLink"><i class="fab fa-instagram"></i></a>
                </div>

                <div class="profile-actions" id="profileActions">
                    <button class="save-btn" id="saveProfileBtn">Salvar</button>
                    <button class="cancel-btn" id="cancelEditBtn">Cancelar</button>
                </div>
            </div>

            <div class="profile-header-top-actions">
                <button class="edit-profile-btn" id="editProfileBtn">Editar Perfil</button>
            </div>
        </div>

        <section class="posts-section">
            <h2>Publicações</h2>
            <div class="posts-grid" id="postsGrid">
                <div class="add-post-card" id="addPostCard">
                    <i class="fas fa-plus plus-icon"></i>
                </div>
            </div>
        </section>
        
    </div>

    <div id="addPostModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Adicionar Nova Publicação</h3>
                <span class="close-button" id="closeModalBtn">&times;</span>
            </div>
            <div class="modal-body">
                <label for="postMediaInput" class="custom-file-upload">
                    <i class="fas fa-upload"></i> Escolher Arquivo
                </label>
                <input type="file" id="postMediaInput" accept="image/*,video/*">
                <img id="mediaPreviewImage" class="modal-preview" alt="Prévia da imagem">
                <video id="mediaPreviewVideo" class="modal-preview" controls muted></video>
                <textarea id="postDescriptionInput" placeholder="Escreva uma legenda para sua publicação..." rows="4"></textarea>
            </div>
            <div class="modal-footer">
                <button class="add-btn" id="createPostBtn">Publicar</button>
                <button class="cancel-modal-btn" id="cancelPostBtn">Cancelar</button>
            </div>
        </div>
    </div>

    <div id="editContactModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Informações de Contato</h3>
                <span class="close-button" id="closeContactModalBtn">&times;</span>
            </div>
            <div class="modal-body">
                <label for="editEmailInput">Email:</label>
                <input type="text" id="editEmailInput" placeholder="Seu email de contato">

                <label for="editWhatsappInput">WhatsApp (Numero):</label>
                <input type="text" id="editWhatsappInput" placeholder="Ex: 5511987654321">

                <label for="editInstagramInput">Instagram (link do perfil ou Usuário):</label>
                <input type="text" id="editInstagramInput" placeholder="Ex: seunome.atleta">
            </div>
            <div class="modal-footer">
                <button class="add-btn" id="saveContactBtn">Salvar Contato</button>
                <button class="cancel-modal-btn" id="cancelContactBtn">Cancelar</button>
            </div>
        </div>
    </div>

    <div id="editPostModal" class="modal edit-post-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Publicação</h3>
                <span class="close-button" id="closeEditPostModalBtn">&times;</span>
            </div>
            <div class="modal-body">
                <label for="editPostMediaInput" class="custom-file-upload">
                    <i class="fas fa-upload"></i> Mudar Arquivo
                </label>
                <input type="file" id="editPostMediaInput" accept="image/*,video/*">
                <img id="editMediaPreviewImage" class="modal-preview" alt="Prévia da imagem">
                <video id="editMediaPreviewVideo" class="modal-preview" controls muted></video>
                <textarea id="editPostDescriptionInput" placeholder="Edite a legenda da sua publicação..." rows="4"></textarea>
            </div>
            <div class="modal-footer">
                <button class="add-btn" id="saveEditedPostBtn">Salvar Edição</button>
                <button class="cancel-modal-btn" id="cancelEditPostBtn">Cancelar</button>
            </div>
        </div>
    </div>

    

    <script>
 
    const currentUserId = <?php echo json_encode($user_id); ?>;
</script>
<script src="Contratante.js"></script>
</body>
</html>