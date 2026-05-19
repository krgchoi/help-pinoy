<?php include './template/header.php'; ?>

<style>
    :root {
        --primary-blue: #003366;
        --secondary-blue: #0057b7;
        --accent-yellow: #FFCC00;
        --light-gray: #f8f9fa;
        --dark-gray: #343a40;
    }

    /* Reset & base styles */
    .coming-soon-wrapper {
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        position: relative;
        overflow: hidden;
    }

    /* Animated background bubbles */
    .bubble {
        position: absolute;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 20s infinite ease-in-out;
        pointer-events: none;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0) rotate(0deg);
        }

        50% {
            transform: translateY(-100px) rotate(180deg);
        }
    }

    .coming-card {
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        border-radius: 30px;
        box-shadow: 0 30px 70px rgba(0, 0, 0, 0.2);
        padding: 60px 50px;
        text-align: center;
        max-width: 650px;
        width: 90%;
        margin: 20px;
        position: relative;
        z-index: 2;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        animation: slideUp 0.6s ease-out;
    }

    .coming-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 40px 80px rgba(0, 0, 0, 0.25);
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .coming-icon {
        width: 130px;
        height: 130px;
        background: linear-gradient(135deg, var(--secondary-blue), var(--primary-blue));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 30px;
        color: white;
        font-size: 3.5rem;
        box-shadow: 0 10px 30px rgba(0, 51, 102, 0.3);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
            box-shadow: 0 10px 30px rgba(0, 51, 102, 0.3);
        }

        50% {
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(0, 51, 102, 0.4);
        }
    }

    .coming-card h1 {
        font-size: 2.8rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        margin-bottom: 15px;
    }

    .coming-card p {
        font-size: 1.15rem;
        color: #6c757d;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    /* Feature list */
    .feature-list {
        text-align: left;
        margin: 30px 0;
        padding: 0;
        list-style: none;
    }

    .feature-list li {
        padding: 10px 0;
        color: #495057;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .feature-list li i {
        color: var(--accent-yellow);
        width: 24px;
        font-size: 1.1rem;
    }

    /* Progress bar */
    .progress-container {
        margin: 30px 0;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        color: var(--dark-gray);
        font-weight: 500;
    }

    .progress-bar-bg {
        background: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
        height: 8px;
    }

    .progress-fill {
        background: linear-gradient(90deg, var(--primary-blue), var(--secondary-blue));
        width: 65%;
        height: 100%;
        border-radius: 10px;
        animation: fillProgress 1.5s ease-out;
        position: relative;
        overflow: hidden;
    }

    .progress-fill::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        animation: shimmer 2s infinite;
    }

    @keyframes fillProgress {
        from {
            width: 0;
        }

        to {
            width: 65%;
        }
    }

    @keyframes shimmer {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(100%);
        }
    }

    .btn-return {
        background: linear-gradient(135deg, var(--accent-yellow), #ffd700);
        border: none;
        color: var(--primary-blue);
        padding: 15px 45px;
        font-weight: 700;
        border-radius: 50px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        margin-top: 20px;
        font-size: 1.1rem;
    }

    .btn-return:hover {
        transform: translateX(-5px);
        background: linear-gradient(135deg, #ffd700, var(--accent-yellow));
        color: var(--primary-blue);
        text-decoration: none;
        box-shadow: 0 5px 15px rgba(255, 204, 0, 0.3);
    }

    /* Notify form */
    .notify-form {
        margin: 30px 0;
        padding-top: 20px;
        border-top: 1px solid #e9ecef;
    }

    .notify-form h3 {
        font-size: 1.2rem;
        color: var(--primary-blue);
        margin-bottom: 15px;
    }

    .input-group {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .input-group input {
        flex: 1;
        padding: 12px 20px;
        border: 2px solid #e9ecef;
        border-radius: 50px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .input-group input:focus {
        outline: none;
        border-color: var(--secondary-blue);
        box-shadow: 0 0 0 3px rgba(0, 87, 183, 0.1);
    }

    .btn-notify {
        background: var(--primary-blue);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-notify:hover {
        background: var(--secondary-blue);
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .coming-card {
            padding: 40px 30px;
        }

        .coming-card h1 {
            font-size: 2rem;
        }

        .input-group {
            flex-direction: column;
        }

        .btn-notify {
            width: 100%;
        }
    }
</style>

<div class="coming-soon-wrapper">
    <!-- Animated bubbles -->
    <?php for ($i = 1; $i <= 8; $i++): ?>
        <div class="bubble" style="
        width: <?= rand(50, 200) ?>px;
        height: <?= rand(50, 200) ?>px;
        left: <?= rand(0, 100) ?>%;
        top: <?= rand(0, 100) ?>%;
        animation-duration: <?= rand(15, 30) ?>s;
        animation-delay: <?= rand(0, 10) ?>s;">
        </div>
    <?php endfor; ?>

    <div class="coming-card">
        <div class="coming-icon">
            <i class="fas fa-cogs"></i>
        </div>

        <h1>Coming Soon</h1>

        <p>
            We're working hard to bring you an amazing experience.
            This feature is currently under development.
        </p>

        <a href="<?= BASE_URL ?>index.php" class="btn-return">
            <i class="fas fa-arrow-left"></i> Return to Homepage
        </a>
    </div>
</div>

<?php include './template/footer.php'; ?>