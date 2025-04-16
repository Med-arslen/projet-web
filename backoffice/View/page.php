<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MovieVibe</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
  <script defer src="https://use.fontawesome.com/releases/v5.1.0/js/all.js" integrity="sha384-3LK/3kTpDE/Pkp8gTNp2gR/2gOiwQ6QaO7Td0zV76UFJVhqLl4Vl3KL1We6q6wR9" crossorigin="anonymous"></script>
  <script src="scriptpage.js"></script>
</head>
<body>
  <div class="wrapper">
    <!-- HEADER -->
    <header>
      <div class="movieVibeLogo">
        <a id="logo" href="#home"><img src="logo.png" alt="MovieVibe Logo" id="logo1"></a>
      </div>      
      <nav class="main-nav">                
        <a href="#home" class="active">Accueil</a>
        <a href="#tvShows">Catalogue</a>
        <a href="#movies">Événement</a>
        <a href="#originals">Boutique</a>
        <a href="#reclamations">Réclamation</a>
      </nav>
      <nav class="sub-nav">
        <div class="search-container">
          <i class="fas fa-search search-icon"></i>
          <input type="text" class="search-bar" placeholder="Titres, personnes, genres">
        </div>
        <a href="#"><i class="fas fa-bell sub-nav-logo"></i></a>
        <a href="#" class="account-menu">
          <img src="https://occ-0-1432-1433.1.nflxso.net/dnm/api/v6/K6hjPJd6cR6FpVELC5Pd6ovHRSk/AAAABQnOnMxhb19v9lQZScL86ZpnI21__HC3fseilqjXbQRegistry4Ixz2m4URJi5eB1_KyqWqjgNHjlJuKPZkavs1YRYbPd5A.png?r=a41" alt="Account">
          <i class="fas fa-caret-down"></i>
        </a>
      </nav>      
    </header>

    <!-- MAIN CONTAINER -->
    <section class="main-container">
      <!-- FEATURED CONTENT -->
      

      <!-- POPULAR MOVIES -->
      <div class="location" id="home">
        <h2>Populaire sur MovieVibe</h2>
        <div class="movie-row">
          <div class="movie-slider">
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/p1.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Movie Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/p2.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Movie Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/tv4.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Movie Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/tv5.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Movie Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/p9.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Movie Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/p12.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Movie Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <button class="slider-arrow prev"><i class="fas fa-chevron-left"></i></button>
          <button class="slider-arrow next"><i class="fas fa-chevron-right"></i></button>
        </div>
      </div>

      <!-- TRENDING NOW -->
      <div class="location" id="trending">
        <h2>Tendances actuelles</h2>
        <div class="movie-row">
          <div class="movie-slider">
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/p1.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Movie Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/p2.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Movie Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/t3.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Movie Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/p2.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Movie Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/t6.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Movie Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <button class="slider-arrow prev"><i class="fas fa-chevron-left"></i></button>
          <button class="slider-arrow next"><i class="fas fa-chevron-right"></i></button>
        </div>
      </div>

      <!-- TV SHOWS -->
      <div class="location" id="tvShows">
        <h2>Séries TV</h2>
        <div class="movie-row">
          <div class="movie-slider">
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/tv1.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Show Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/tv3.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Show Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/tv4.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Show Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/tv5.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Show Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/tv6.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Show Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/p2.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Show Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/tv7.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Show Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/tv8.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Show Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/tv4.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Show Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/tv5.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Show Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
            <div class="movie-item">
              <img src="https://github.com/carlosavilae/Netflix-Clone/blob/master/img/tv11.PNG?raw=true" alt="">
              <div class="movie-info">
                <h3>Show Title</h3>
                <div class="mini-comment">
                  <form>
                    <input type="text" name="comment" placeholder="✍️ Donnez votre avis..." />
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <button class="slider-arrow prev"><i class="fas fa-chevron-left"></i></button>
          <button class="slider-arrow next"><i class="fas fa-chevron-right"></i></button>
        </div>
      </div>
    </section>

    <!-- LINKS -->
    <section class="link">
      <div class="logos">
        <a href="#"><i class="fab fa-facebook-square fa-2x logo"></i></a>
        <a href="#"><i class="fab fa-instagram fa-2x logo"></i></a>
        <a href="#"><i class="fab fa-twitter fa-2x logo"></i></a>
        <a href="#"><i class="fab fa-youtube fa-2x logo"></i></a>
      </div>
      <div class="sub-links">
        <ul>
          <li><a href="#">Audio et sous-titres</a></li>
          <li><a href="#">Description audio</a></li>
          <li><a href="#">Centre d'aide</a></li>
          <li><a href="#">Cartes cadeaux</a></li>
          <li><a href="#">Relations investisseurs</a></li>
          <li><a href="#">Recrutement</a></li>
          <li><a href="#">Conditions d'utilisation</a></li>
          <li><a href="#">Confidentialité</a></li>
          <li><a href="#">Mentions légales</a></li>
          <li><a href="#">Préférences de cookies</a></li>
          <li><a href="#">Informations de l'entreprise</a></li>
          <li><a href="#">Nous contacter</a></li>
        </ul>
      </div>
    </section>

    <!-- FOOTER -->
    <footer>
      <p>&copy; 2025 MovieVibe. Tous droits réservés.</p>
    </footer>
  </div>
</body>
</html>