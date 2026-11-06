// Universal Bell Notification System
function createBellNotification() {
    // Create bell HTML
    const bellHTML = `
        <li class="nav-item">
            <a class="nav-link position-relative bell-notification" href="#" onclick="showNotifications()" id="notificationBell">
                <i class="fas fa-bell bell-icon"></i>
                <span class="notification-badge" id="notificationCount">0</span>
                <div class="bell-glow"></div>
            </a>
        </li>
    `;
    
    // Add notification modal HTML
    const modalHTML = `
        <div class="modal fade" id="notificationModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(90deg, #ff7f50, #ff6347); color: white;">
                        <h5 class="modal-title"><i class="fas fa-bell"></i> Food Acceptance Notifications</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="notificationContent">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add CSS styles
    const bellCSS = `
        <style>
        .bell-notification {
            position: relative !important;
            padding: 8px 12px !important;
            border-radius: 50px !important;
            transition: all 0.3s ease !important;
            overflow: visible !important;
        }
        
        .bell-notification:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            transform: scale(1.1) !important;
        }
        
        .bell-icon {
            font-size: 20px !important;
            color: #ffd700 !important;
            filter: drop-shadow(0 0 8px rgba(255, 215, 0, 0.6)) !important;
            transition: all 0.3s ease !important;
            animation: bellGlow 2s ease-in-out infinite alternate !important;
        }
        
        .bell-notification:hover .bell-icon {
            animation: bellRing 0.5s ease-in-out !important;
            color: #ffed4e !important;
            filter: drop-shadow(0 0 15px rgba(255, 215, 0, 0.9)) !important;
        }
        
        .notification-badge {
            position: absolute !important;
            top: -5px !important;
            right: -5px !important;
            background: linear-gradient(45deg, #ff4757, #ff3742) !important;
            color: white !important;
            border-radius: 50% !important;
            width: 22px !important;
            height: 22px !important;
            font-size: 11px !important;
            font-weight: bold !important;
            display: none !important;
            align-items: center !important;
            justify-content: center !important;
            border: 2px solid white !important;
            box-shadow: 0 2px 8px rgba(255, 71, 87, 0.4) !important;
            animation: badgePulse 1.5s ease-in-out infinite !important;
        }
        
        .notification-badge.show {
            display: flex !important;
        }
        
        .bell-glow {
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            width: 40px !important;
            height: 40px !important;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.3), transparent 70%) !important;
            border-radius: 50% !important;
            opacity: 0 !important;
            transition: opacity 0.3s ease !important;
            pointer-events: none !important;
        }
        
        .bell-notification:hover .bell-glow {
            opacity: 1 !important;
            animation: glowPulse 1s ease-in-out infinite !important;
        }
        
        @keyframes bellGlow {
            0% { filter: drop-shadow(0 0 5px rgba(255, 215, 0, 0.4)); }
            100% { filter: drop-shadow(0 0 12px rgba(255, 215, 0, 0.8)); }
        }
        
        @keyframes bellRing {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-15deg); }
            75% { transform: rotate(15deg); }
        }
        
        @keyframes badgePulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        @keyframes glowPulse {
            0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.3; }
            50% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.6; }
        }
        
        /* Special effects for new notifications */
        .bell-notification.new-notification .bell-icon {
            animation: newNotificationAlert 2s ease-in-out infinite !important;
        }
        
        @keyframes newNotificationAlert {
            0%, 100% { 
                color: #ffd700; 
                filter: drop-shadow(0 0 8px rgba(255, 215, 0, 0.6)); 
            }
            50% { 
                color: #ff4757; 
                filter: drop-shadow(0 0 15px rgba(255, 71, 87, 0.8)); 
                transform: scale(1.1) rotate(15deg); 
            }
        }
        </style>
    `;
    
    // Add CSS to head
    document.head.insertAdjacentHTML('beforeend', bellCSS);
    
    // Find navbar and add bell
    const navbar = document.querySelector('.navbar-nav');
    if (navbar) {
        navbar.insertAdjacentHTML('beforeend', bellHTML);
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Load notifications
    loadNotifications();
    setInterval(loadNotifications, 10000); // Check every 10 seconds
}

function loadNotifications() {
    fetch('get_notifications.php')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notificationCount');
            const bell = document.getElementById('notificationBell');
            
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.classList.add('show');
                bell.classList.add('new-notification');
            } else {
                badge.classList.remove('show');
                bell.classList.remove('new-notification');
            }
        })
        .catch(error => console.log('Notification error:', error));
}

// Show notifications in modal
function showNotifications() {
    const modal = new bootstrap.Modal(document.getElementById('notificationModal'));
    const content = document.getElementById('notificationContent');
    
    // Show loading
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Load notifications
    fetch('get_accepted_donations.php')
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                let html = '';
                data.forEach(donation => {
                    html += `
                        <div class="alert alert-success mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-3" style="font-size: 24px;"></i>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><strong>${donation.accepted_by}</strong> accepted donation</h6>
                                    <p class="mb-1"><strong>Donor:</strong> ${donation.donor_name}</p>
                                    <p class="mb-1"><strong>Food:</strong> ${donation.food_type} (${donation.quantity})</p>
                                    <p class="mb-1"><strong>Location:</strong> ${donation.location}</p>
                                    <small class="text-muted">Accepted on ${new Date(donation.accepted_at).toLocaleString()}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
                content.innerHTML = html;
            } else {
                content.innerHTML = `
                    <div class="text-center text-muted">
                        <i class="fas fa-bell-slash" style="font-size: 48px; opacity: 0.3;"></i>
                        <p class="mt-3">No food acceptances yet.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Error loading notifications.
                </div>
            `;
        });
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    createBellNotification();
});