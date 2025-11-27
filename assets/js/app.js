const API_URL = 'http://localhost/redsocial/backend/api';

// Helper para Fetch
const fetchData = async (endpoint, method = 'GET', body = null) => {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    if (body) {
        options.body = JSON.stringify(body);
    }
    try {
        const response = await fetch(`${API_URL}/${endpoint}`, options);
        return await response.json();
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error', 'Hubo un problema de conexión', 'error');
        return null;
    }
};

// Auth Functions
const login = async (email, password) => {
    const data = await fetchData('login.php', 'POST', { email, password });
    if (data && data.status === 'success') {
        await Swal.fire('Bienvenido', data.message, 'success');
        window.location.href = 'index.html';
    } else {
        Swal.fire('Error', data ? data.message : 'Error desconocido', 'error');
    }
};

const register = async (userData) => {
    const data = await fetchData('register.php', 'POST', userData);
    if (data && data.message === 'Usuario registrado exitosamente') {
        await Swal.fire('Éxito', 'Registro completado, por favor inicia sesión', 'success');
        window.location.href = 'login.html';
    } else {
        Swal.fire('Error', data ? data.message : 'Error al registrar', 'error');
    }
};

const logout = async () => {
    await fetchData('logout.php');
    window.location.href = 'login.html';
};

const checkAuth = async () => {
    const data = await fetchData('check_auth.php');
    if (!data || !data.is_logged_in) {
        window.location.href = 'login.html';
    }
    return data;
};

// Feed Functions
const loadPosts = async () => {
    const response = await fetchData('get_posts.php');
    const container = document.getElementById('feedContainer');
    container.innerHTML = '';

    if (response && response.data) {
        response.data.forEach(post => {
            container.innerHTML += renderPost(post);
        });
    }
};

const renderPost = (post) => {
    const commentsHtml = post.comentarios.map(comment => `
        <div class="d-flex mb-2">
            <img src="${comment.autor_foto || 'https://via.placeholder.com/32'}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
            <div class="bg-light p-2 rounded w-100">
                <h6 class="mb-0 small fw-bold">${comment.autor_nombre}</h6>
                <p class="mb-0 small">${comment.contenido}</p>
            </div>
        </div>
    `).join('');

    return `
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white border-0 d-flex align-items-center">
                <img src="${post.autor_foto || 'https://via.placeholder.com/40'}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                <div>
                    <h6 class="mb-0 fw-bold">${post.autor_nombre}</h6>
                    <small class="text-muted">${post.fecha_publicacion}</small>
                </div>
            </div>
            <div class="card-body">
                <p class="card-text">${post.contenido}</p>
                ${post.imagen ? `<img src="${post.imagen}" class="img-fluid rounded mb-3">` : ''}
            </div>
            <div class="card-footer bg-white">
                <div class="mb-3" id="comments-${post.id_publicacion}">
                    ${commentsHtml}
                </div>
                <div class="d-flex">
                    <input type="text" class="form-control me-2" placeholder="Escribe un comentario..." id="comment-input-${post.id_publicacion}">
                    <button class="btn btn-outline-primary btn-sm" onclick="handleAddComment(${post.id_publicacion})">Enviar</button>
                </div>
            </div>
        </div>
    `;
};

const handleCreatePost = async () => {
    const content = document.getElementById('postContent').value;
    if (!content.trim()) return;

    const result = await fetchData('create_post.php', 'POST', { contenido: content });
    if (result && result.message === 'Publicación creada') {
        document.getElementById('postContent').value = '';
        loadPosts();
        Swal.fire({
            icon: 'success',
            title: 'Publicado',
            showConfirmButton: false,
            timer: 1500
        });
    } else {
        Swal.fire('Error', 'No se pudo publicar', 'error');
    }
};

const handleAddComment = async (postId) => {
    const input = document.getElementById(`comment-input-${postId}`);
    const content = input.value;
    if (!content.trim()) return;

    const result = await fetchData('add_comment.php', 'POST', { id_publicacion: postId, contenido: content });
    if (result && result.message === 'Comentario agregado') {
        input.value = '';
        loadPosts(); // Reload to show new comment
    } else {
        Swal.fire('Error', 'No se pudo comentar', 'error');
    }
};

// Profile Functions
const loadProfile = async () => {
    const data = await fetchData('get_profile.php');
    if (data && data.id_usuario) {
        document.getElementById('profileName').textContent = data.nombre;
        document.getElementById('profileBio').textContent = data.biografia || 'Sin biografía';
        if (data.foto_perfil) {
            document.getElementById('profileImage').src = data.foto_perfil;
        }

        // Fill modal
        document.getElementById('editName').value = data.nombre;
        document.getElementById('editBio').value = data.biografia || '';
    }
};

const loadUserPosts = async () => {
    const response = await fetchData('get_user_posts.php');
    const container = document.getElementById('userFeedContainer');
    container.innerHTML = '';

    if (response && response.data) {
        response.data.forEach(post => {
            container.innerHTML += renderPost(post);
        });
    } else {
        container.innerHTML = '<p class="text-center text-muted">No has publicado nada aún.</p>';
    }
};

const handleUpdateProfile = async () => {
    const name = document.getElementById('editName').value;
    const bio = document.getElementById('editBio').value;
    const photoInput = document.getElementById('editPhoto');

    const formData = new FormData();
    formData.append('nombre', name);
    formData.append('biografia', bio);

    if (photoInput.files[0]) {
        formData.append('foto_perfil', photoInput.files[0]);
    }

    try {
        const response = await fetch(`${API_URL}/update_profile.php`, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result && result.message === 'Perfil actualizado') {
            await Swal.fire('Éxito', 'Perfil actualizado correctamente', 'success');
            location.reload();
        } else {
            Swal.fire('Error', result ? result.message : 'Error al actualizar', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error', 'Hubo un problema de conexión', 'error');
    }
};
