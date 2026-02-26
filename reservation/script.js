const VALID_EMAIL = "admin@gmail.com";
const VALID_PASSWORD = "admin123";
let isLoggedIn = false;
let selectedRoom = "";
let selectedPrice = "";

// DOM Elements
const loginModal = document.getElementById('loginModal');
const loginNavBtn = document.getElementById('loginNavBtn');
const historySection = document.getElementById('history-section');
const historyBody = document.getElementById('historyBody');
const activeCard = document.getElementById('activeBookingCard');
const navStatus = document.getElementById('navStatusLabel');

// 1. Dashboard Updater (Fetches JSON from PHP)
async function updateDashboard() {
    if (!isLoggedIn) return;
    try {
        const response = await fetch('status_check.php');
        const data = await response.json();
        
        navStatus.innerText = "Status: " + data.currentStatus;
        historySection.style.display = "block";
        document.getElementById('historyNav').style.display = "block";

        // Build History Table Rows
        if (data.history && data.history.length > 0) {
            historyBody.innerHTML = data.history.map(row => `
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding:15px;">${row.room_name}</td>
                    <td style="padding:15px;"><span class="status-${row.status}">${row.status}</span></td>
                    <td style="padding:15px; color: #27ae60; font-weight: bold;">${row.price || 'N/A'}</td>
                    <td style="padding:15px; color: #888;">${row.created_at}</td>
                </tr>
            `).join('');
        } else {
            historyBody.innerHTML = '<tr><td colspan="4" style="padding:20px; text-align:center;">No history found.</td></tr>';
        }

        // Active Booking Visibility
        const isActive = (data.currentStatus === "Pending" || data.currentStatus === "Confirmed");
        if (isActive) {
            activeCard.style.display = "block";
            document.getElementById('activeDetails').innerText = `You have an active booking for: ${data.roomName}`;
            document.querySelectorAll('.book-room-btn, .book-now-btn').forEach(b => b.disabled = true);
        } else {
            activeCard.style.display = "none";
            document.querySelectorAll('.book-room-btn, .book-now-btn').forEach(b => b.disabled = false);
        }
    } catch (e) {
        console.error("Error syncing dashboard. Check status_check.php output.");
    }
}

// 2. Handle Booking Button Clicks
document.querySelectorAll('.book-room-btn, .book-now-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        selectedRoom = e.target.getAttribute('data-room');
        selectedPrice = e.target.getAttribute('data-price');
        
        if (!isLoggedIn) {
            loginModal.classList.add('show');
        } else {
            executeBooking();
        }
    });
});

// 3. Send Booking to PHP
async function executeBooking() {
    try {
        const response = await fetch('reserve.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `room_name=${encodeURIComponent(selectedRoom)}&price=${encodeURIComponent(selectedPrice)}`
        });
        const data = await response.json();
        alert("Booking request sent for " + selectedRoom + " at " + selectedPrice);
        updateDashboard();
    } catch (e) {
        alert("Server error. Ensure reserve.php returns JSON.");
    }
}

// 4. End Reservation (Check-out)
document.getElementById('checkoutBtn').addEventListener('click', async () => {
    if(confirm("Would you like to end your current stay? This will allow you to book again.")) {
        await fetch('checkout.php');
        updateDashboard();
    }
});

// 5. Login/Logout Logic
document.getElementById('loginForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const emailInput = document.getElementById('email').value;
    const passInput = document.getElementById('password').value;

    if (emailInput === VALID_EMAIL && passInput === VALID_PASSWORD) {
        isLoggedIn = true;
        loginModal.classList.remove('show');
        loginNavBtn.innerText = "Logout";
        updateDashboard();
    } else {
        alert("Invalid login details.");
    }
});

loginNavBtn.onclick = (e) => {
    e.preventDefault();
    if (isLoggedIn) {
        if(confirm("Logout from Serene Haven?")) location.reload();
    } else {
        loginModal.classList.add('show');
    }
};

document.querySelector('.close-btn').onclick = () => loginModal.classList.remove('show');
window.onclick = (event) => { if (event.target == loginModal) loginModal.classList.remove('show'); };