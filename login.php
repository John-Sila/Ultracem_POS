<?php
session_start();

if (isset($_SESSION['username'])) {
    header("Location: dash.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="uc_styles.css?v=<?php echo filemtime('uc_styles.css'); ?>">
</head>
<body>
    <div class="loginMainDiv">
        <p class="declare">Optimabyte 1.2.7</p>
        <hr />

        <form class="modern-form" action="process_login.php" method="POST">
            <div class="form-title">
                <img src="images/optimabyte_logo.png" alt="Sign in">
            </div>

            <div class="form-body">
                <div class="input-group">
                <div class="input-wrapper">
                    <svg fill="none" viewBox="0 0 24 24" class="input-icon">
                    <circle
                        stroke-width="1.5"
                        stroke="currentColor"
                        r="4"
                        cy="8"
                        cx="12"
                    ></circle>
                    <path
                        stroke-linecap="round"
                        stroke-width="1.5"
                        stroke="currentColor"
                        d="M5 20C5 17.2386 8.13401 15 12 15C15.866 15 19 17.2386 19 20"
                    ></path>
                    </svg>
                    <input required="" placeholder="Username" name="login_username" class="form-input" type="text"
                    />
                </div>
                </div>

                <div class="input-group">
                    <div class="input-wrapper">
                        <svg fill="none" viewBox="0 0 24 24" class="input-icon">
                        <path
                            stroke-width="1.5"
                            stroke="currentColor"
                            d="M12 10V14M8 6H16C17.1046 6 18 6.89543 18 8V16C18 17.1046 17.1046 18 16 18H8C6.89543 18 6 17.1046 6 16V8C6 6.89543 6.89543 6 8 6Z"
                        ></path>
                        </svg>
                        <input required="" placeholder="Password" name="login_password" class="form-input" type="password"/>
                        <button class="password-toggle" type="button">
                            <svg fill="none" viewBox="0 0 24 24" class="eye-icon">
                            <path
                            stroke-width="1.5"
                            stroke="currentColor"
                            d="M2 12C2 12 5 5 12 5C19 5 22 12 22 12C22 12 19 19 12 19C5 19 2 12 2 12Z"
                            ></path>
                            <circle
                            stroke-width="1.5"
                            stroke="currentColor"
                            r="3"
                            cy="12"
                            cx="12"
                            ></circle>
                        </svg>
                        </button>
                    </div>
                </div>

                <div class="input-group">
                    <div class="input-wrapper">
                        
                        <select name="selectCompany" class="form-input-selection" id="selectCompany">
                            <optgroup>
                                <option value="ultracem">Ultracem</option>
                                <option value="optimabyte">OptimaByte</option>
                            </optgroup>
                        </select>
                    </div>
                </div>
            </div>

            <button class="submit-button" type="submit">
                <span class="button-text">Log in</span>
                <div class="button-glow"></div>
            </button>
        </form>


        <hr />
    </div>
</body>
</html>