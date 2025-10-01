const profilePicture = document.getElementById('profilePicture');
const profilePictureInput = document.getElementById('profilePictureInput');
const profileContainer = document.getElementById('profileContainer');
const editProfileBtn = document.getElementById('editProfileBtn');
const saveProfileBtn = document.getElementById('saveProfileBtn');
const cancelEditBtn = document.getElementById('cancelEditBtn');
const profileActions = document.getElementById('profileActions');
const profilePictureWrapper = document.getElementById('profilePictureWrapper');

const emailLink = document.getElementById('emailLink');
const whatsappLink = document.getElementById('whatsappLink');
const instagramLink = document.getElementById('instagramLink');

const editContactModal = document.getElementById('editContactModal');
const editEmailInput = document.getElementById('editEmailInput');
const editWhatsappInput = document.getElementById('editWhatsappInput');
const editInstagramInput = document.getElementById('editInstagramInput');
const closeContactModalBtn = document.getElementById('closeContactModalBtn');
const cancelContactBtn = document.getElementById('cancelContactBtn');
const saveContactBtn = document.getElementById('saveContactBtn');

const addPostCard = document.getElementById('addPostCard');
const addPostModal = document.getElementById('addPostModal');
const closeModalBtn = document.getElementById('closeModalBtn');
const cancelPostBtn = document.getElementById('cancelPostBtn');
const postMediaInput = document.getElementById('postMediaInput');
const mediaPreviewImage = document.getElementById('mediaPreviewImage');
const mediaPreviewVideo = document.getElementById('mediaPreviewVideo');
const postDescriptionInput = document.getElementById('postDescriptionInput');
const createPostBtn = document.getElementById('createPostBtn');

const editPostModal = document.getElementById('editPostModal');
const closeEditPostModalBtn = document.getElementById('closeEditPostModalBtn');
const editPostMediaInput = document.getElementById('editPostMediaInput');
const editMediaPreviewImage = document.getElementById('editMediaPreviewImage');
const editMediaPreviewVideo = document.getElementById('editMediaPreviewVideo');
const editPostDescriptionInput = document.getElementById('editPostDescriptionInput');
const saveEditedPostBtn = document.getElementById('saveEditedPostBtn');
const cancelEditPostBtn = document.getElementById('cancelEditPostBtn');
let currentEditingPostElement = null;


const editableFields = {
    athleteName: { view: document.getElementById('athleteName'), edit: document.getElementById('editAthleteName') },
    username: { view: document.getElementById('usernameText'), edit: document.getElementById('editUsername') },
    bio: { view: document.getElementById('bioText'), edit: document.getElementById('editBio') },
    sport: { view: document.getElementById('sportText'), edit: document.getElementById('editSport') },
    emailLink: { view: emailLink },
    whatsappLink: { view: whatsappLink },
    instagramLink: { view: instagramLink }
};

let originalValues = {};
let originalProfilePicSrc = '';


async function loadProfile() {
    const userIdToFetch = (!currentUserId || currentUserId === 'null') ? 1 : currentUserId;

    try {
        const resp = await fetch(`api/get_profile.php?user_id=${userIdToFetch}`);
        const data = await resp.json();

        if (!resp.ok || !data.success) {
            console.warn('Perfil não encontrado no backend ou erro na API. Exibindo dados padrão.');
            editableFields.athleteName.view.textContent = 'Nome do Atleta Padrão';
            editableFields.username.view.textContent = 'usuario_padrao';
            editableFields.bio.view.textContent = 'Bio padrão do atleta.';
            editableFields.sport.view.textContent = 'Esporte Padrão';
            return;
        }

        const profile = data.profile;

        editableFields.athleteName.view.textContent = profile.nome_completo || profile.usuario || 'Nome do Atleta';
        editableFields.username.view.textContent = profile.username || profile.usuario || 'usuario';
        editableFields.bio.view.textContent = profile.bio || '';
        editableFields.sport.view.textContent = profile.modalidade || '';

        if (profile.foto_perfil_url) {
            profilePicture.src = profile.foto_perfil_url;
        } else {
           
        }
        originalProfilePicSrc = profilePicture.src;

        editableFields.athleteName.edit.value = profile.nome_completo || profile.usuario || '';
        editableFields.username.edit.value = profile.username || profile.usuario || '';
        editableFields.bio.edit.value = profile.bio || '';
        editableFields.sport.edit.value = profile.modalidade || '';

        emailLink.href = profile.email_contato ? `mailto:${profile.email_contato}` : '#';

        const whatsappNum = profile.whatsapp && !profile.whatsapp.startsWith('55') ? `55${profile.whatsapp}` : profile.whatsapp;
        whatsappLink.href = whatsappNum ? `https://wa.me/${whatsappNum}` : '#';

        const instagramHandle = profile.instagram ? profile.instagram.replace(/^@/, '') : '';
        instagramLink.href = instagramHandle ? `https://www.instagram.com/${instagramHandle}/` : '#';

        for (const key in editableFields) {
            if (editableFields[key].edit) {
                originalValues[key] = editableFields[key].edit.value;
            }
        }
        originalValues.emailLink = emailLink.href;
        originalValues.whatsappLink = whatsappLink.href;
        originalValues.instagramLink = instagramLink.href;

        editEmailInput.value = profile.email_contato || '';
        editWhatsappInput.value = profile.whatsapp || '';
        editInstagramInput.value = instagramHandle;

    } catch (err) {
        console.error('Erro na requisição Fetch (loadProfile):', err);
        alert('Não foi possível carregar o perfil: ' + err.message);
    }
}


function toggleEditMode(enable) {
    if (enable) {
        profileContainer.classList.add('edit-mode');
        profilePictureWrapper.classList.add('edit-mode');
        editProfileBtn.style.display = 'none';
        profileActions.style.display = 'flex';

        for (const key in editableFields) {
            if (editableFields[key].edit) {
                originalValues[key] = editableFields[key].edit.value;
            }
        }
        originalProfilePicSrc = profilePicture.src;
        originalValues.emailLink = emailLink.href;
        originalValues.whatsappLink = whatsappLink.href;
        originalValues.instagramLink = instagramLink.href;

    } else {
        profileContainer.classList.remove('edit-mode');
        profilePictureWrapper.classList.remove('edit-mode');
        editProfileBtn.style.display = 'block';
        profileActions.style.display = 'none';
    }
}


editProfileBtn.addEventListener('click', () => toggleEditMode(true));

saveProfileBtn.addEventListener('click', async () => {
    const formData = new FormData();


    formData.append('nome_completo', editableFields.athleteName.edit.value);
    formData.append('username', editableFields.username.edit.value);
    formData.append('bio', editableFields.bio.edit.value);
    formData.append('modalidade', editableFields.sport.edit.value);
    formData.append('user_id', currentUserId);

    if (profilePictureInput.files.length > 0) {
        formData.append('foto_perfil', profilePictureInput.files[0]);
    } else if (profilePicture.src.includes('')) {
    
    }


    try {
        const resp = await fetch('api/update_profile.php', {
            method: 'POST',
            body: formData
        });

        const data = await resp.json();

        if (!resp.ok || !data.success) {
            throw new Error(data.message || 'Falha ao salvar perfil');
        }

        alert('Perfil atualizado com sucesso!');

        if (data.foto_perfil_url) {
            profilePicture.src = data.foto_perfil_url;
            originalProfilePicSrc = data.foto_perfil_url;
        } else {
            
        }


        editableFields.athleteName.view.textContent = editableFields.athleteName.edit.value;
        editableFields.username.view.textContent = editableFields.username.edit.value;
        editableFields.bio.view.textContent = editableFields.bio.edit.value;
        editableFields.sport.view.textContent = editableFields.sport.edit.value;

        toggleEditMode(false);

    } catch (err) {
        console.error('Erro ao salvar perfil:', err);
        alert('Erro ao salvar perfil: ' + err.message);
    }
});

cancelEditBtn.addEventListener('click', () => {
    for (const key in editableFields) {
        if (editableFields[key].edit) {
            editableFields[key].edit.value = originalValues[key];
        }
    }
    profilePicture.src = originalProfilePicSrc;

    emailLink.href = originalValues.emailLink;
    whatsappLink.href = originalValues.whatsappLink;
    instagramLink.href = originalValues.instagramLink;

    toggleEditMode(false);
});

profilePictureInput.addEventListener('change', e => {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = ev => profilePicture.src = ev.target.result;
        reader.readAsDataURL(file);
    } else {
        profilePicture.src = originalProfilePicSrc;
    }
});


function openContactModal(event) {
    if (profileContainer.classList.contains('edit-mode')) {
        event.preventDefault();
        editContactModal.style.display = 'flex';

        editEmailInput.value = emailLink.href.includes('mailto:') ? emailLink.href.replace('mailto:', '') : '';

        const whatsappMatch = whatsappLink.href.match(/\d+/g);
        editWhatsappInput.value = whatsappMatch ? whatsappMatch.join('') : '';

        const instaMatch = instagramLink.href.match(/\/([a-zA-Z0-9._]+)\/?$/);
        editInstagramInput.value = instaMatch ? instaMatch[1].replace(/^@/, '') : '';
    }
}

emailLink.addEventListener('click', openContactModal);
whatsappLink.addEventListener('click', openContactModal);
instagramLink.addEventListener('click', openContactModal);

closeContactModalBtn.addEventListener('click', () => {
    editContactModal.style.display = 'none';
});

cancelContactBtn.addEventListener('click', () => {
    editContactModal.style.display = 'none';
});


window.addEventListener('click', e => {
    if (e.target == editContactModal) {
        editContactModal.style.display = 'none';
    }
    if (e.target == editPostModal) {
        editPostModal.style.display = 'none';
        currentEditingPostElement = null;
    }

    document.querySelectorAll('.post-options-menu').forEach(menu => {
        if (menu.style.display === 'flex' && !menu.contains(e.target) && !e.target.closest('.post-options-btn')) {
            menu.style.display = 'none';
        }
    });
});

saveContactBtn.addEventListener('click', async () => {
    const updatedContactData = {
        email_contato: editEmailInput.value.trim(),
        whatsapp: editWhatsappInput.value.trim(),
        instagram: editInstagramInput.value.trim().startsWith('@') ? editInstagramInput.value.trim().substring(1) : editInstagramInput.value.trim(),
        user_id: currentUserId
    };

    for (const key in updatedContactData) {
        if (!updatedContactData[key]) {
            updatedContactData[key] = null;
        }
    }

    try {
        const resp = await fetch('api/update_profile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(updatedContactData)
        });
        const data = await resp.json();

        if (!resp.ok || !data.success) {
            throw new Error(data.message || 'Falha ao atualizar contato');
        }

        alert('Informações de contato atualizadas com sucesso!');

        emailLink.href = updatedContactData.email_contato ? `mailto:${updatedContactData.email_contato}` : '#';

        const whatsappNum = updatedContactData.whatsapp && !updatedContactData.whatsapp.startsWith('55')
            ? `55${updatedContactData.whatsapp}`
            : updatedContactData.whatsapp;
        whatsappLink.href = whatsappNum ? `https://wa.me/${whatsappNum}` : '#';

        const instagramHandle = updatedContactData.instagram ? updatedContactData.instagram.replace(/^@/, '') : '';
        instagramLink.href = instagramHandle ? `https://www.instagram.com/${instagramHandle}/` : '#';

        editContactModal.style.display = 'none';

        loadProfile();
    } catch (err) {
        console.error('Erro na requisição Fetch (salvar contato):', err);
        alert('Erro ao salvar informações de contato: ' + err.message);
    }
});


addPostCard.addEventListener('click', () => {
    addPostModal.style.display = 'flex';
    postMediaInput.value = '';
    mediaPreviewImage.src = '';
    mediaPreviewImage.style.display = 'none';
    mediaPreviewVideo.src = '';
    mediaPreviewVideo.style.display = 'none';
    postDescriptionInput.value = '';
});

closeModalBtn.addEventListener('click', () => {
    addPostModal.style.display = 'none';
});

cancelPostBtn.addEventListener('click', () => {
    addPostModal.style.display = 'none';
});

postMediaInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (event) => {
            if (file.type.startsWith('image/')) {
                mediaPreviewImage.src = event.target.result;
                mediaPreviewImage.style.display = 'block';
                mediaPreviewVideo.style.display = 'none';
            } else if (file.type.startsWith('video/')) {
                mediaPreviewVideo.src = event.target.result;
                mediaPreviewVideo.style.display = 'block';
                mediaPreviewImage.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);
    } else {
        mediaPreviewImage.style.display = 'none';
        mediaPreviewVideo.style.display = 'none';
    }
});

createPostBtn.addEventListener('click', async () => {
    const file = postMediaInput.files[0];
    const description = postDescriptionInput.value.trim();

    if (!file && !description) {
        alert('Por favor, selecione uma imagem/vídeo ou escreva uma legenda para sua publicação.');
        return;
    }

    const formData = new FormData();
    if (file) {
        formData.append('media_file', file);
    }
    formData.append('description', description);
    formData.append('user_id', currentUserId);

    try {
        const response = await fetch('api/create_post.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (response.ok && result.success) {
            alert('Publicação criada com sucesso!');
            addPostModal.style.display = 'none';
            loadPosts(); 
        } else {
            throw new Error(result.message || 'Erro desconhecido ao criar publicação.');
        }
    }
    catch (error) {
        console.error('Erro ao publicar:', error);
        alert('Não foi possível publicar: ' + error.message);
    }
});


function addPostToGrid(postData) {
    const postsGrid = document.getElementById('postsGrid');
    const addPostCardElement = document.getElementById('addPostCard');

    const postFullCard = document.createElement('div');
    postFullCard.classList.add('post-full-card');
    postFullCard.dataset.postId = postData.id;

    const postItem = document.createElement('div');
    postItem.classList.add('post-item');

    let mediaElement;
    if (postData.mediaType === 'image') {
        mediaElement = document.createElement('img');
        mediaElement.src = postData.mediaUrl;
        mediaElement.alt = postData.description || 'Publicação de imagem';
    } else if (postData.mediaType === 'video') {
        mediaElement = document.createElement('video');
        mediaElement.src = postData.mediaUrl;
        mediaElement.controls = true;
        mediaElement.muted = true;
        mediaElement.autoplay = false;
    }
    if (mediaElement) {
        postItem.appendChild(mediaElement);
    }

    postFullCard.appendChild(postItem);

    if (postData.description) {
        const postCaption = document.createElement('p');
        postCaption.classList.add('post-caption');
        postCaption.textContent = postData.description;
        postFullCard.appendChild(postCaption);
    }

    const postActions = document.createElement('div');
    postActions.classList.add('post-actions');

    const likeButton = document.createElement('button');
    likeButton.classList.add('like-button');
    const likeIcon = document.createElement('i');
    likeIcon.classList.add('fas', 'fa-heart');
    if (postData.isLiked) {
        likeIcon.classList.add('liked');
    }
    const likeCount = document.createElement('span');
    likeCount.classList.add('like-count');
    likeCount.textContent = postData.likes || 0;
    likeButton.appendChild(likeIcon);
    likeButton.appendChild(document.createTextNode(' '));
    likeButton.appendChild(likeCount);
    postActions.appendChild(likeButton);

    likeButton.addEventListener('click', async (event) => {
        event.stopPropagation();
        const currentLikes = parseInt(likeCount.textContent);
        const isLiked = likeIcon.classList.contains('liked');

        try {
            const response = await fetch('api/toggle_like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ post_id: postData.id })
            });
            const result = await response.json();

            if (result.success) {
                if (result.action === 'liked') {
                    likeCount.textContent = currentLikes + 1;
                    likeIcon.classList.add('liked');
                } else {
                    likeCount.textContent = currentLikes - 1;
                    likeIcon.classList.remove('liked');
                }
            } else {
                throw new Error(result.message || 'Erro ao curtir/descurtir.');
            }
        } catch (error) {
            console.error('Erro na ação de curtir:', error);
            alert('Não foi possível realizar a ação de curtir.');
        }
    });

    postFullCard.appendChild(postActions);

    const postOptionsBtn = document.createElement('button');
    postOptionsBtn.classList.add('post-options-btn');
    postOptionsBtn.innerHTML = '<i class="fas fa-ellipsis-v"></i>';
    postFullCard.appendChild(postOptionsBtn);

    const postOptionsMenu = document.createElement('div');
    postOptionsMenu.classList.add('post-options-menu');

    const editPostOption = document.createElement('button');
    editPostOption.innerHTML = '<i class="fas fa-edit"></i> Editar';
    editPostOption.addEventListener('click', (event) => {
        event.stopPropagation();
        postOptionsMenu.style.display = 'none';
        openEditPostModal(postFullCard, postData);
    });
    postOptionsMenu.appendChild(editPostOption);

    const deletePostOption = document.createElement('button');
    deletePostOption.classList.add('delete-option');
    deletePostOption.innerHTML = '<i class="fas fa-trash-alt"></i> Excluir';
    deletePostOption.addEventListener('click', async (event) => {
        event.stopPropagation();
        postOptionsMenu.style.display = 'none';
        if (confirm('Tem certeza que deseja excluir esta publicação? Esta ação é irreversível.')) {
            await deletePost(postFullCard);
        }
    });
    postOptionsMenu.appendChild(deletePostOption);

    postFullCard.appendChild(postOptionsMenu);

    postOptionsBtn.addEventListener('click', (event) => {
        event.stopPropagation();
        document.querySelectorAll('.post-options-menu').forEach(menu => {
            if (menu !== postOptionsMenu) {
                menu.style.display = 'none';
            }
        });
        postOptionsMenu.style.display = postOptionsMenu.style.display === 'flex' ? 'none' : 'flex';
    });


    const addCard = document.getElementById('addPostCard');
    if (addCard && addCard.parentNode === postsGrid) {
        const firstPost = postsGrid.querySelector('.post-full-card');
        if (firstPost) {
            postsGrid.insertBefore(postFullCard, firstPost);
        } else {
            postsGrid.appendChild(postFullCard);
        }
    } else {
        postsGrid.appendChild(postFullCard);
    }
}


async function loadPosts() {
    const postsGrid = document.getElementById('postsGrid');
    const addPostCardElement = document.getElementById('addPostCard');

    Array.from(postsGrid.children).forEach(child => {
        if (child !== addPostCardElement) {
            child.remove();
        }
    });

    if (!addPostCardElement.parentNode) {
        postsGrid.appendChild(addPostCardElement);
    }

    postsGrid.insertBefore(addPostCardElement, postsGrid.firstChild);


    const userIdToFetch = (!currentUserId || currentUserId === 'null') ? 1 : currentUserId;

    try {

        const response = await fetch(`api/get_posts_by_user.php?user_id=${userIdToFetch}`);
        const data = await response.json();

        if (data.success) {

            data.posts.forEach(post => {

                const newPostFullCard = document.createElement('div');
                newPostFullCard.classList.add('post-full-card');
                newPostFullCard.dataset.postId = post.id;

                const postItem = document.createElement('div');
                postItem.classList.add('post-item');
                let mediaElement;
                if (post.mediaType === 'image') {
                    mediaElement = document.createElement('img');
                    mediaElement.src = post.mediaUrl;
                    mediaElement.alt = post.description || 'Publicação de imagem';
                } else if (post.mediaType === 'video') {
                    mediaElement = document.createElement('video');
                    mediaElement.src = post.mediaUrl;
                    mediaElement.controls = true;
                    mediaElement.muted = true;
                    mediaElement.autoplay = false;
                }
                if (mediaElement) {
                    postItem.appendChild(mediaElement);
                }
                newPostFullCard.appendChild(postItem);

                if (post.description) {
                    const postCaption = document.createElement('p');
                    postCaption.classList.add('post-caption');
                    postCaption.textContent = post.description;
                    newPostFullCard.appendChild(postCaption);
                }

                const postActions = document.createElement('div');
                postActions.classList.add('post-actions');

                const likeButton = document.createElement('button');
                likeButton.classList.add('like-button');
                const likeIcon = document.createElement('i');
                likeIcon.classList.add('fas', 'fa-heart');
                if (post.isLiked) {
                    likeIcon.classList.add('liked');
                }
                const likeCount = document.createElement('span');
                likeCount.classList.add('like-count');
                likeCount.textContent = post.likes || 0;
                likeButton.appendChild(likeIcon);
                likeButton.appendChild(document.createTextNode(' '));
                likeButton.appendChild(likeCount);
                postActions.appendChild(likeButton);
                likeButton.addEventListener('click', async (event) => {
                    event.stopPropagation();
                    const currentLikes = parseInt(likeCount.textContent);
                    const isLiked = likeIcon.classList.contains('liked');
                    try {
                        const response = await fetch('api/toggle_like.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ post_id: post.id })
                        });
                        const result = await response.json();
                        if (result.success) {
                            if (result.action === 'liked') {
                                likeCount.textContent = currentLikes + 1;
                                likeIcon.classList.add('liked');
                            } else {
                                likeCount.textContent = currentLikes - 1;
                                likeIcon.classList.remove('liked');
                            }
                        } else {
                            throw new Error(result.message || 'Erro ao curtir/descurtir.');
                        }
                    } catch (error) {
                        console.error('Erro na ação de curtir:', error);
                        alert('Não foi possível realizar a ação de curtir.');
                    }
                });

                newPostFullCard.appendChild(postActions);

                const postOptionsBtn = document.createElement('button');
                postOptionsBtn.classList.add('post-options-btn');
                postOptionsBtn.innerHTML = '<i class="fas fa-ellipsis-v"></i>';
                newPostFullCard.appendChild(postOptionsBtn);

                const postOptionsMenu = document.createElement('div');
                postOptionsMenu.classList.add('post-options-menu');

                const editPostOption = document.createElement('button');
                editPostOption.innerHTML = '<i class="fas fa-edit"></i> Editar';
                editPostOption.addEventListener('click', (event) => {
                    event.stopPropagation();
                    postOptionsMenu.style.display = 'none';
                    openEditPostModal(newPostFullCard, post);
                });
                postOptionsMenu.appendChild(editPostOption);

                const deletePostOption = document.createElement('button');
                deletePostOption.classList.add('delete-option');
                deletePostOption.innerHTML = '<i class="fas fa-trash-alt"></i> Excluir';
                deletePostOption.addEventListener('click', async (event) => {
                    event.stopPropagation();
                    postOptionsMenu.style.display = 'none';
                    if (confirm('Tem certeza que deseja excluir esta publicação? Esta ação é irreversível.')) {
                        await deletePost(newPostFullCard);
                    }
                });
                postOptionsMenu.appendChild(deletePostOption);
                newPostFullCard.appendChild(postOptionsMenu);

                postOptionsBtn.addEventListener('click', (event) => {
                    event.stopPropagation();
                    document.querySelectorAll('.post-options-menu').forEach(menu => {
                        if (menu !== postOptionsMenu) {
                            menu.style.display = 'none';
                        }
                    });
                    postOptionsMenu.style.display = postOptionsMenu.style.display === 'flex' ? 'none' : 'flex';
                });

                postsGrid.insertBefore(newPostFullCard, addPostCardElement.nextSibling);

            });
        } else {
            console.error('Erro ao carregar publicações:', data.message);
        }
    } catch (err) {
        console.error('Erro ao carregar publicações:', err);
        const postsGrid = document.getElementById('postsGrid');
        postsGrid.innerHTML = `
            <div class="error-message">
                Não foi possível carregar as publicações. Por favor, tente novamente mais tarde.
            </div>
        `;
    }
}

async function deletePost(postElement) {
    const postId = postElement.dataset.postId;
    try {
        const response = await fetch('api/delete_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ post_id: postId })
        });
        const result = await response.json();
        if (response.ok && result.success) {
            alert('Publicação excluída com sucesso!');
            postElement.remove();
        } else {
            throw new Error(result.message || 'Erro desconhecido ao excluir publicação.');
        }
    } catch (error) {
        console.error('Erro ao excluir publicação:', error);
        alert('Não foi possível excluir a publicação: ' + error.message);
    }
}

function openEditPostModal(postElement, postData) {
    currentEditingPostElement = postElement;
    editPostModal.style.display = 'flex';
    editPostDescriptionInput.value = postData.description || '';
    editPostMediaInput.value = '';

    const isImage = postData.mediaType === 'image';
    const isVideo = postData.mediaType === 'video';

    editMediaPreviewImage.style.display = isImage ? 'block' : 'none';
    editMediaPreviewVideo.style.display = isVideo ? 'block' : 'none';

    if (isImage) {
        editMediaPreviewImage.src = postData.mediaUrl;
    }
    if (isVideo) {
        editMediaPreviewVideo.src = postData.mediaUrl;
    }
}

closeEditPostModalBtn.addEventListener('click', () => {
    editPostModal.style.display = 'none';
    currentEditingPostElement = null;
});

cancelEditPostBtn.addEventListener('click', () => {
    editPostModal.style.display = 'none';
    currentEditingPostElement = null;
});

editPostMediaInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (event) => {
            if (file.type.startsWith('image/')) {
                editMediaPreviewImage.src = event.target.result;
                editMediaPreviewImage.style.display = 'block';
                editMediaPreviewVideo.style.display = 'none';
            } else if (file.type.startsWith('video/')) {
                editMediaPreviewVideo.src = event.target.result;
                editMediaPreviewVideo.style.display = 'block';
                editMediaPreviewImage.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);
    } else {
        editMediaPreviewImage.style.display = 'none';
        editMediaPreviewVideo.style.display = 'none';
    }
});

saveEditedPostBtn.addEventListener('click', async () => {
    if (!currentEditingPostElement) return;

    const postId = currentEditingPostElement.dataset.postId;
    const description = editPostDescriptionInput.value.trim();
    const file = editPostMediaInput.files[0];

    const formData = new FormData();
    formData.append('post_id', postId);
    formData.append('description', description);
    if (file) {
        formData.append('media_file', file);
    }

    try {
        const response = await fetch('api/update_post.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (response.ok && result.success) {
            alert('Publicação atualizada com sucesso!');
            editPostModal.style.display = 'none';
            loadPosts();
        } else {
            throw new Error(result.message || 'Erro desconhecido ao atualizar publicação.');
        }
    } catch (error) {
        console.error('Erro ao salvar edição:', error);
        alert('Não foi possível salvar a edição: ' + error.message);
    }
});


document.addEventListener('DOMContentLoaded', () => {
    loadProfile();
    loadPosts();
});