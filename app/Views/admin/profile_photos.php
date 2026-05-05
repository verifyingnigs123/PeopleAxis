<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<style>
    .profile-photos-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .page-header h1 {
        margin: 0;
        color: #2f5f45;
        font-size: 1.8rem;
    }

    .statistics {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        color: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .stat-card h3 {
        margin: 0 0 10px 0;
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .stat-card .number {
        font-size: 2.5rem;
        font-weight: bold;
    }

    .role-section {
        margin-bottom: 40px;
    }

    .role-header {
        background: #f8fafb;
        padding: 15px 20px;
        border-left: 4px solid #2f5f45;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .role-header h2 {
        margin: 0;
        color: #2f5f45;
        font-size: 1.3rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .role-header .count {
        background: #2f5f45;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .photos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .photo-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .photo-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .photo-image {
        width: 100%;
        height: 200px;
        background: linear-gradient(135deg, #2f5f45 0%, #6ea988 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
        font-weight: bold;
        overflow: hidden;
        position: relative;
    }

    .photo-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .photo-info {
        padding: 15px;
    }

    .photo-name {
        font-weight: 600;
        color: #2f5f45;
        margin-bottom: 5px;
        font-size: 0.95rem;
    }

    .photo-email {
        color: #64748b;
        font-size: 0.85rem;
        margin-bottom: 10px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .photo-meta {
        font-size: 0.8rem;
        color: #94a3b8;
        margin-bottom: 10px;
    }

    .photo-actions {
        display: flex;
        gap: 8px;
    }

    .photo-actions button {
        flex: 1;
        padding: 6px 10px;
        border: none;
        border-radius: 4px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-view {
        background: #2f5f45;
        color: white;
    }

    .btn-view:hover {
        background: #1f4028;
    }

    .btn-delete {
        background: #e74c3c;
        color: white;
    }

    .btn-delete:hover {
        background: #c0392b;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #94a3b8;
        font-size: 0.95rem;
    }

    .empty-state i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 15px;
        display: block;
    }
</style>

<div class="profile-photos-container">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1><i class="fas fa-image"></i> Profile Photos Gallery</h1>
            <p style="margin: 5px 0 0 0; color: #64748b; font-size: 0.9rem;">
                View and manage all profile photos organized by user role
            </p>
        </div>
        <div style="text-align: right;">
            <small style="color: #94a3b8; display: block; margin-bottom: 10px;">
                Last updated: <?= date('M d, Y H:i') ?>
            </small>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="statistics" id="statsContainer">
        <div class="stat-card">
            <h3>Total Photos</h3>
            <div class="number" id="totalCount">0</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #1e5a5e 0%, #3d9ca8 100%);">
            <h3>Super Admin</h3>
            <div class="number" id="count-super-admin">0</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #5a3c1f 0%, #a87d3d 100%);">
            <h3>HR Admin</h3>
            <div class="number" id="count-hr-admin">0</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #3d3a1f 0%, #7d7d3d 100%);">
            <h3>Manager</h3>
            <div class="number" id="count-manager">0</div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #3d1f5a 0%, #7d3da8 100%);">
            <h3>Employee</h3>
            <div class="number" id="count-employee">0</div>
        </div>
    </div>

    <!-- Photos by Role -->
    <?php 
    $roles = ['Super Admin', 'HR Admin', 'Manager', 'Employee'];
    foreach ($roles as $role): 
        $photos = $photosByRole[$role] ?? [];
        $count = $statistics[$role] ?? 0;
    ?>
        <div class="role-section">
            <div class="role-header">
                <h2>
                    <?= esc($role) ?>
                    <span class="count"><?= count($photos) ?> shown of <?= $count ?></span>
                </h2>
            </div>

            <?php if (count($photos) > 0): ?>
                <div class="photos-grid">
                    <?php foreach ($photos as $photo): ?>
                        <div class="photo-card">
                            <div class="photo-image">
                                <?php if (!empty($photo['file_path']) && is_file(FCPATH . $photo['file_path'])): ?>
                                    <img src="<?= base_url($photo['file_path']) ?>" alt="<?= esc($photo['name'] ?? 'Profile') ?>">
                                <?php else: ?>
                                    <?= strtoupper(substr(trim($photo['name'] ?? 'U'), 0, 1)) ?>
                                <?php endif; ?>
                            </div>
                            <div class="photo-info">
                                <div class="photo-name"><?= esc($photo['name'] ?? 'Unknown') ?></div>
                                <div class="photo-email"><?= esc($photo['email'] ?? 'N/A') ?></div>
                                <div class="photo-meta">
                                    <i class="fas fa-file-image"></i> <?= $photo['file_size'] ? round($photo['file_size'] / 1024, 2) . ' KB' : 'N/A' ?>
                                </div>
                                <div class="photo-meta">
                                    <i class="fas fa-calendar"></i> <?= $photo['uploaded_at'] ? date('M d, Y', strtotime($photo['uploaded_at'])) : 'N/A' ?>
                                </div>
                                <div class="photo-actions">
                                    <button class="btn-view" onclick="viewPhoto('<?= esc($photo['file_path']) ?>', '<?= esc($photo['name']) ?>')">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <?php if (session()->get('role_name') === 'Super Admin'): ?>
                                        <button class="btn-delete" onclick="deletePhoto(<?= $photo['id'] ?>)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-image"></i>
                    <p>No profile photos available for <?= esc($role) ?></p>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal for viewing full image -->
<div id="imageModal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.7); align-items:center; justify-content:center;">
    <div style="background:white; padding:20px; border-radius:8px; max-width:600px; max-height:80vh; overflow:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
            <h3 id="modalTitle" style="margin:0; color:#2f5f45;"></h3>
            <button onclick="closeModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>
        <img id="modalImage" src="" alt="Profile Photo" style="width:100%; height:auto; border-radius:8px;">
    </div>
</div>

<script>
    function viewPhoto(filePath, name) {
        document.getElementById('imageModal').style.display = 'flex';
        document.getElementById('modalImage').src = '<?= base_url() ?>' + filePath;
        document.getElementById('modalTitle').textContent = 'Profile: ' + name;
    }

    function closeModal() {
        document.getElementById('imageModal').style.display = 'none';
    }

    function deletePhoto(photoId) {
        if (!confirm('Are you sure you want to delete this profile photo?')) {
            return;
        }

        fetch(`<?= base_url('admin/profile-photos/delete') ?>/${photoId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to delete photo'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the photo');
        });
    }

    // Load statistics on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadStatistics();
    });

    function loadStatistics() {
        fetch('<?= base_url('admin/profile-photos/stats') ?>', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.total !== undefined) {
                document.getElementById('totalCount').textContent = data.total;
                document.getElementById('count-super-admin').textContent = data.by_role['Super Admin'] || 0;
                document.getElementById('count-hr-admin').textContent = data.by_role['HR Admin'] || 0;
                document.getElementById('count-manager').textContent = data.by_role['Manager'] || 0;
                document.getElementById('count-employee').textContent = data.by_role['Employee'] || 0;
            }
        })
        .catch(error => console.error('Error loading statistics:', error));
    }

    // Close modal on outside click
    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>

<?= $this->endSection() ?>
