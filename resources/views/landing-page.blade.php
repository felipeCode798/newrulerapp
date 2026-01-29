<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Ruler Soluciones De Tránsito</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicons -->
    <link href="http://127.0.0.1:8000/images/favicon_black.png" rel="icon">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #1e3a8a; /* blue-900 */
            --secondary-color: #3b82f6; /* blue-600 */
            --accent-color: #10b981; /* emerald-500 */
        }
        
        body {
            font-family: 'Open Sans', sans-serif;
            scroll-behavior: smooth;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }
        
        .section-header {
            text-align: center;
            padding-bottom: 40px;
        }
        
        .section-header h3 {
            font-size: 36px;
            font-weight: 700;
            position: relative;
            color: #1f2937;
        }
        
        .section-header h3::after {
            content: '';
            position: absolute;
            display: block;
            width: 50px;
            height: 3px;
            background: var(--secondary-color);
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .section-header p {
            margin: 20px auto 0 auto;
            color: #6b7280;
            max-width: 800px;
        }
        
        /* Estilos para el carrusel principal */
        .main-carousel-item {
            height: 80vh;
            min-height: 400px;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .main-carousel-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
        }
        
        .carousel-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            z-index: 2;
            max-width: 800px;
            padding: 0 20px;
        }
        
        .carousel-content h2 {
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .carousel-content p {
            font-size: 20px;
            margin-bottom: 30px;
        }
        
        /* Carrusel de servicios */
        .services-carousel-container {
            position: relative;
            padding: 0 50px;
        }
        
        .services-carousel {
            overflow: hidden;
            width: 100%;
        }
        
        .services-track {
            display: flex;
            transition: transform 0.5s ease;
            gap: 30px;
        }
        
        .service-slide {
            flex: 0 0 calc(33.333% - 20px);
            min-width: 300px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .service-slide:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .service-slide i {
            font-size: 40px;
            color: var(--secondary-color);
            margin-bottom: 20px;
        }
        
        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: var(--secondary-color);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            transition: all 0.3s ease;
        }
        
        .carousel-btn:hover {
            background: var(--primary-color);
            transform: translateY(-50%) scale(1.1);
        }
        
        .carousel-btn.prev {
            left: 0;
        }
        
        .carousel-btn.next {
            right: 0;
        }
        
        .carousel-dots {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 10px;
        }
        
        .carousel-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #d1d5db;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .carousel-dot.active {
            background: var(--secondary-color);
            transform: scale(1.2);
        }
        
        .featured-box {
            background: white;
            padding: 40px 30px;
            border-radius: 10px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            height: 100%;
        }
        
        .featured-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .featured-box i {
            font-size: 48px;
            color: var(--secondary-color);
            margin-bottom: 20px;
        }
        
        .about-col {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            height: 100%;
            transition: all 0.3s ease;
        }
        
        .about-col:hover {
            transform: translateY(-5px);
        }
        
        .testimonial-item {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin: 20px;
        }
        
        .contact-info-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            height: 100%;
        }
        
        .contact-info-box i {
            font-size: 40px;
            color: var(--secondary-color);
            margin-bottom: 20px;
        }
        
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--secondary-color);
            color: white;
            margin: 0 5px;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
        }
        
        .back-to-top {
            position: fixed;
            right: 30px;
            bottom: 30px;
            width: 50px;
            height: 50px;
            background: var(--secondary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .back-to-top:hover {
            background: var(--primary-color);
            transform: translateY(-5px);
        }
        
        .nav-link {
            position: relative;
            padding: 10px 15px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: var(--secondary-color);
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--secondary-color);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .nav-link:hover::after {
            width: 80%;
        }
        
        .consulta-section {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            border-radius: 20px;
            padding: 50px 30px;
            margin: 80px auto;
            max-width: 1200px;
            box-shadow: 0 20px 40px rgba(30, 58, 138, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .consulta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('http://127.0.0.1:8000/images/intro-carousel/aerial-views-with-cars-streets.png');
            background-size: cover;
            background-position: center;
            opacity: 0.1;
            z-index: 1;
        }
        
        .consulta-section > * {
            position: relative;
            z-index: 2;
        }
        
        /* Responsive para móviles */
        @media (max-width: 768px) {
            .service-slide {
                flex: 0 0 calc(100% - 20px);
            }
            
            .carousel-content h2 {
                font-size: 32px;
            }
            
            .carousel-content p {
                font-size: 16px;
            }
        }
        
        @media (min-width: 769px) and (max-width: 1024px) {
            .service-slide {
                flex: 0 0 calc(50% - 20px);
            }
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-lg fixed w-full z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <div class="flex items-center">
                    <img src="http://127.0.0.1:8000/images/Logo_ruler.png" alt="Ruler Soluciones" class="h-16 w-auto">
                </div>
                
                <!-- Navegación -->
                <nav class="hidden md:flex space-x-8">
                    <a href="#inicio" class="nav-link">Inicio</a>
                    <a href="#nosotros" class="nav-link">Nosotros</a>
                    <a href="#servicios" class="nav-link">Servicios</a>
                    <a href="#consulta" class="nav-link">Consulta Trámites</a>
                    <a href="#contacto" class="nav-link">Contacto</a>
                </nav>
                
                <!-- Botón Panel Admin -->
                <a href="/admin" class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2 rounded-lg font-semibold transition duration-300 shadow-md">
                    <i class="fas fa-sign-in-alt mr-2"></i> Panel Admin
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Carousel -->
    <section id="inicio" class="pt-20">
        <div class="relative">
            <!-- Carrusel Principal -->
            <div id="mainCarousel" class="relative overflow-hidden">
                <div class="main-carousel-track flex transition-transform duration-500 ease-in-out">
                    <!-- Slide 1 -->
                    <div class="main-carousel-item flex-shrink-0 w-full" style="background-image: url('http://127.0.0.1:8000/images/intro-carousel/aerial-views-with-cars-streets.png');">
                        <div class="carousel-content">
                            <h2 class="animate__animated animate__fadeInDown">Curso de Conductores</h2>
                            <p class="animate__animated animate__fadeInUp">Explora nuestro curso de conductores diseñado para mejorar tus habilidades en la carretera y promover prácticas seguras.</p>
                        </div>
                    </div>
                    
                    <!-- Slide 2 -->
                    <div class="main-carousel-item flex-shrink-0 w-full" style="background-image: url('http://127.0.0.1:8000/images/intro-carousel/brunette-businesswoman-inside-car.png');">
                        <div class="carousel-content">
                            <h2>Soluciones Eficientes para tus Infracciones</h2>
                            <p>Con nuestras soluciones personalizadas, transformamos el manejo de comparendos en un proceso rápido y sin complicaciones.</p>
                        </div>
                    </div>
                    
                    <!-- Slide 3 -->
                    <div class="main-carousel-item flex-shrink-0 w-full" style="background-image: url('http://127.0.0.1:8000/images/intro-carousel/person-taking-driver-s-license-exam.png');">
                        <div class="carousel-content">
                            <h2>Actualiza Tu Documentación con Facilidad</h2>
                            <p>Simplificamos el proceso de renovación de licencias y documentos vehiculares para que puedas estar siempre al día.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Controles del carrusel principal -->
                <button class="carousel-btn prev" onclick="prevMainSlide()">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-btn next" onclick="nextMainSlide()">
                    <i class="fas fa-chevron-right"></i>
                </button>
                
                <!-- Indicadores del carrusel principal -->
                <div class="carousel-dots" id="mainCarouselDots">
                    <span class="carousel-dot active" onclick="goToMainSlide(0)"></span>
                    <span class="carousel-dot" onclick="goToMainSlide(1)"></span>
                    <span class="carousel-dot" onclick="goToMainSlide(2)"></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Servicios Destacados -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="featured-box animate__animated animate__fadeInUp">
                    <i class="fas fa-bookmark"></i>
                    <h4 class="text-xl font-bold mb-3">Compromiso con el Cumplimiento</h4>
                    <p class="text-gray-600">Nos comprometemos a garantizar el cumplimiento riguroso de las normativas viales.</p>
                </div>
                
                <div class="featured-box animate__animated animate__fadeInUp animate__delay-1s">
                    <i class="fas fa-clock"></i>
                    <h4 class="text-xl font-bold mb-3">Eficiencia en el Tiempo</h4>
                    <p class="text-gray-600">Puedes confiar en soluciones rápidas y eficientes para tus necesidades de tránsito.</p>
                </div>
                
                <div class="featured-box animate__animated animate__fadeInUp animate__delay-2s">
                    <i class="fas fa-heart"></i>
                    <h4 class="text-xl font-bold mb-3">Atención Personalizada al Cliente</h4>
                    <p class="text-gray-600">Nos comprometemos a brindarte una atención personalizada y dedicada en cada paso del camino.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Nosotros -->
    <section id="nosotros" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="section-header">
                <h3>Nosotros</h3>
                <p>Nuestro enfoque se centra en tres pilares fundamentales: cumplimiento, eficiencia y atención al cliente. Trabajamos incansablemente para garantizar que nuestros clientes cumplan con todas las regulaciones viales de manera precisa y oportuna.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="about-col animate__animated animate__fadeInUp">
                    <img src="http://127.0.0.1:8000/images/mision.jpg" alt="Misión" class="w-full h-48 object-cover rounded-t-lg mb-4">
                    <h4 class="text-xl font-bold mb-3">Misión</h4>
                    <p class="text-gray-600">En Ruler Soluciones de Tránsito, nuestra misión es liderar la transformación del paisaje vial, proporcionando soluciones innovadoras y eficientes que promuevan la seguridad, la movilidad y el cumplimiento de las normativas viales.</p>
                </div>
                
                <div class="about-col animate__animated animate__fadeInUp">
                    <img src="http://127.0.0.1:8000/images/objetivos.jpg" alt="Objetivos" class="w-full h-48 object-cover rounded-t-lg mb-4">
                    <h4 class="text-xl font-bold mb-3">Objetivos</h4>
                    <p class="text-gray-600">Nuestros objetivos abarcan la excelencia en el servicio al cliente, la continua innovación en soluciones de tránsito, el estricto cumplimiento normativo y el desarrollo del talento de nuestro equipo.</p>
                </div>
                
                <div class="about-col animate__animated animate__fadeInUp">
                    <img src="http://127.0.0.1:8000/images/vision.jpeg" alt="Visión" class="w-full h-48 object-cover rounded-t-lg mb-4">
                    <h4 class="text-xl font-bold mb-3">Visión</h4>
                    <p class="text-gray-600">Nuestra visión es ser reconocidos como líderes en la industria de soluciones de tránsito, tanto a nivel local como internacional.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Servicios (Carrusel) -->
    <section id="servicios" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="section-header">
                <h3>Servicios</h3>
                <p>En Ruler Soluciones de Tránsito, ofrecemos una amplia gama de servicios diseñados para cubrir todas tus necesidades relacionadas con el tráfico y la movilidad.</p>
            </div>
            
            <!-- Carrusel de servicios -->
            <div class="services-carousel-container">
                <button class="carousel-btn prev" onclick="prevServiceSlide()">
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                <div class="services-carousel">
                    <div class="services-track" id="servicesTrack">
                        <!-- Slide 1 -->
                        <div class="service-slide">
                            <i class="fas fa-chart-line"></i>
                            <h4 class="text-lg font-bold mb-3">Gestión de Comparendos</h4>
                            <p class="text-gray-600">Simplificamos el proceso de manejo de comparendos, brindándote asesoramiento experto.</p>
                        </div>
                        
                        <!-- Slide 2 -->
                        <div class="service-slide">
                            <i class="fas fa-id-card"></i>
                            <h4 class="text-lg font-bold mb-3">Renovación de Licencias</h4>
                            <p class="text-gray-600">Facilitamos el proceso de renovación de licencias y documentos vehiculares.</p>
                        </div>
                        
                        <!-- Slide 3 -->
                        <div class="service-slide">
                            <i class="fas fa-graduation-cap"></i>
                            <h4 class="text-lg font-bold mb-3">Curso de Conductores</h4>
                            <p class="text-gray-600">Mejora tus habilidades en la carretera con nuestro curso diseñado para todos los niveles.</p>
                        </div>
                        
                        <!-- Slide 4 -->
                        <div class="service-slide">
                            <i class="fas fa-balance-scale"></i>
                            <h4 class="text-lg font-bold mb-3">Resolución de Controversias</h4>
                            <p class="text-gray-600">Nuestro equipo experto te respalda en la resolución de controversias viales.</p>
                        </div>
                        
                        <!-- Slide 5 -->
                        <div class="service-slide">
                            <i class="fas fa-handshake"></i>
                            <h4 class="text-lg font-bold mb-3">Acuerdos de Pago</h4>
                            <p class="text-gray-600">Te ofrecemos asesoramiento personalizado y opciones de pago flexibles.</p>
                        </div>
                        
                        <!-- Slide 6 -->
                        <div class="service-slide">
                            <i class="fas fa-file-contract"></i>
                            <h4 class="text-lg font-bold mb-3">Revocatorias y Prescripciones</h4>
                            <p class="text-gray-600">Manejamos procesos de revocatorias y prescripciones de licencias.</p>
                        </div>
                    </div>
                </div>
                
                <button class="carousel-btn next" onclick="nextServiceSlide()">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
            <!-- Indicadores del carrusel de servicios -->
            <div class="carousel-dots" id="serviceDots">
                <span class="carousel-dot active" onclick="goToServiceSlide(0)"></span>
                <span class="carousel-dot" onclick="goToServiceSlide(1)"></span>
                <span class="carousel-dot" onclick="goToServiceSlide(2)"></span>
            </div>
        </div>
    </section>

    <!-- SECCIÓN DE CONSULTA DE TRÁMITES -->
    <section id="consulta" class="consulta-section">
        <div class="container mx-auto px-4">
            <div class="text-center mb-10">
                <h2 class="text-4xl font-bold text-white mb-4">Consulta el Estado de tus Trámites</h2>
                <p class="text-xl text-blue-100">Ingresa tu cédula para verificar el progreso de tus cursos, licencias y renovaciones</p>
            </div>
            
            <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-8">
                <form action="{{ route('buscar.procesos') }}" method="GET" class="space-y-6">
                    <div>
                        <label class="block text-gray-700 text-left mb-2 font-semibold">
                            <i class="fas fa-id-card text-blue-600 mr-2"></i> Número de Cédula
                        </label>
                        <input type="text" name="cedula" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Ej: 1234567890" required>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-left mb-2 font-semibold">
                            <i class="fas fa-user-tie text-blue-600 mr-2"></i> Tipo de Usuario
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50 transition duration-300">
                                <input type="radio" name="tipo" value="cliente" class="mr-3" checked>
                                <div>
                                    <i class="fas fa-user text-blue-600 text-xl mb-1"></i>
                                    <p class="font-semibold">Cliente</p>
                                    <p class="text-sm text-gray-600">Consultar mis trámites</p>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-green-50 transition duration-300">
                                <input type="radio" name="tipo" value="tramitador" class="mr-3">
                                <div>
                                    <i class="fas fa-user-tie text-green-600 text-xl mb-1"></i>
                                    <p class="font-semibold">Tramitador</p>
                                    <p class="text-sm text-gray-600">Consultar trámites gestionados</p>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-bold py-3 px-4 rounded-lg transition duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-search mr-2"></i> Consultar Estado de Trámites
                    </button>
                </form>
                
                @if(session('error'))
                    <div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg animate__animated animate__shakeX">
                        <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-16 bg-gradient-to-r from-blue-900 to-blue-700 text-white">
        <div class="container mx-auto px-4 text-center">
            <h3 class="text-3xl font-bold mb-6">¡Contáctanos hoy mismo y simplifica tus trámites de tránsito!</h3>
            <p class="text-xl mb-8 max-w-3xl mx-auto">¿Necesitas ayuda con la gestión de comparendos, la renovación de licencias o la resolución de controversias viales? ¡No esperes más!</p>
            <a href="https://api.whatsapp.com/send?phone=573104736884&text=Hola%F0%9F%91%8B%2C%20Quiero%20mas%20informacion%20para%20realizar%20un%20tramite%F0%9F%91%AE%E2%80%8D%E2%99%82%EF%B8%8F" 
               target="_blank" 
               class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-lg transition duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                <i class="fab fa-whatsapp text-2xl mr-3"></i>
                <span class="text-lg">Contactar por WhatsApp</span>
            </a>
        </div>
    </section>

    <!-- Testimonios -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="section-header">
                <h3>Testimonios</h3>
                <p>Lo que dicen nuestros clientes satisfechos</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="testimonial-item">
                    <img src="http://127.0.0.1:8000/images/testimonial-1.jpg" alt="Testimonio 1" class="w-20 h-20 rounded-full mx-auto mb-4">
                    <h4 class="font-bold text-lg mb-2">Saul Goodman</h4>
                    <p class="text-gray-600 italic">"¡Increíble servicio! Gracias a Ruler Soluciones de Tránsito, pude resolver un problema de comparendos que me tenía preocupado durante meses."</p>
                </div>
                
                <div class="testimonial-item">
                    <img src="http://127.0.0.1:8000/images/testimonial-2.jpg" alt="Testimonio 2" class="w-20 h-20 rounded-full mx-auto mb-4">
                    <h4 class="font-bold text-lg mb-2">Sara Wilsson</h4>
                    <p class="text-gray-600 italic">"He estado renovando mi licencia de conducir con Ruler durante años, ¡y nunca me han decepcionado! Su proceso es rápido, fácil y sin complicaciones."</p>
                </div>
                
                <div class="testimonial-item">
                    <img src="http://127.0.0.1:8000/images/testimonial-3.jpg" alt="Testimonio 3" class="w-20 h-20 rounded-full mx-auto mb-4">
                    <h4 class="font-bold text-lg mb-2">Jena Karlis</h4>
                    <p class="text-gray-600 italic">"Tuve un problema legal complicado relacionado con un accidente de tráfico, y Ruler Soluciones de Tránsito estuvo a mi lado en cada paso del camino."</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contacto -->
    <section id="contacto" class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="section-header">
                <h3>Contacto</h3>
                <p>¿Necesitas ayuda con tus trámites de tránsito? ¿Tienes alguna pregunta sobre nuestros servicios? ¡Estamos aquí para ayudarte!</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="contact-info-box">
                    <i class="fas fa-map-marker-alt"></i>
                    <h4 class="text-lg font-bold mb-3">Dirección</h4>
                    <p class="text-gray-600">Calle 56 # 3 - 26, Cali Valle del Cauca</p>
                </div>
                
                <div class="contact-info-box">
                    <i class="fas fa-phone"></i>
                    <h4 class="text-lg font-bold mb-3">Teléfono</h4>
                    <p class="text-gray-600">
                        <a href="tel:+573104736884" class="text-blue-600 hover:text-blue-800">+57 310 473 68 84</a>
                    </p>
                </div>
                
                <div class="contact-info-box">
                    <i class="fas fa-envelope"></i>
                    <h4 class="text-lg font-bold mb-3">Email</h4>
                    <p class="text-gray-600">
                        <a href="mailto:infosoluciones@rulersas.com" class="text-blue-600 hover:text-blue-800">infosoluciones@rulersas.com</a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <!-- Logo e info -->
                <div>
                    <img src="http://127.0.0.1:8000/images/Logo_ruler.png" alt="Ruler Soluciones" class="h-32 w-auto mb-4">
                    <p class="text-gray-400">Gestión profesional de trámites de tránsito</p>
                </div>
                
                <!-- Enlaces rápidos -->
                <div>
                    <h4 class="text-xl font-bold mb-4">Enlaces Rápidos</h4>
                    <ul class="space-y-2">
                        <li><a href="#inicio" class="text-gray-400 hover:text-white transition duration-300"><i class="fas fa-chevron-right mr-2 text-blue-500"></i> Inicio</a></li>
                        <li><a href="#nosotros" class="text-gray-400 hover:text-white transition duration-300"><i class="fas fa-chevron-right mr-2 text-blue-500"></i> Nosotros</a></li>
                        <li><a href="#servicios" class="text-gray-400 hover:text-white transition duration-300"><i class="fas fa-chevron-right mr-2 text-blue-500"></i> Servicios</a></li>
                        <li><a href="#consulta" class="text-gray-400 hover:text-white transition duration-300"><i class="fas fa-chevron-right mr-2 text-blue-500"></i> Consulta Trámites</a></li>
                    </ul>
                </div>
                
                <!-- Redes sociales -->
                <div>
                    <h4 class="text-xl font-bold mb-4">Síguenos</h4>
                    <div class="social-links flex space-x-3">
                        <a href="https://www.facebook.com/Rulersolucionesjuridicas" target="_blank" class="facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/ruler_solucionesdetransito" target="_blank" class="instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://api.whatsapp.com/send?phone=573104736884" target="_blank" class="whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                    <div class="mt-6">
                        <h5 class="font-semibold mb-2">Horario de atención</h5>
                        <p class="text-gray-400">Lunes a Viernes: 8:00 AM - 6:00 PM</p>
                        <p class="text-gray-400">Sábados: 9:00 AM - 1:00 PM</p>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-700 pt-6 text-center">
                <p class="text-gray-400">&copy; {{ date('Y') }} Ruler Soluciones De Tránsito. Todos los derechos reservados.</p>
                <p class="text-gray-500 text-sm mt-2">Diseñado por Juan Felipe Gonzalez</p>
            </div>
        </div>
    </footer>

    <!-- Botón volver arriba -->
    <a href="#inicio" class="back-to-top">
        <i class="fas fa-chevron-up"></i>
    </a>

    <!-- JavaScript -->
    <script>
        // ========== CARRUSEL PRINCIPAL ==========
        let mainCurrentSlide = 0;
        const mainSlides = document.querySelectorAll('.main-carousel-item');
        const mainTrack = document.querySelector('.main-carousel-track');
        const mainDots = document.querySelectorAll('#mainCarouselDots .carousel-dot');
        
        function updateMainCarousel() {
            // Mover el track
            mainTrack.style.transform = `translateX(-${mainCurrentSlide * 100}%)`;
            
            // Actualizar puntos
            mainDots.forEach((dot, index) => {
                dot.classList.toggle('active', index === mainCurrentSlide);
            });
        }
        
        function nextMainSlide() {
            mainCurrentSlide = (mainCurrentSlide + 1) % mainSlides.length;
            updateMainCarousel();
        }
        
        function prevMainSlide() {
            mainCurrentSlide = (mainCurrentSlide - 1 + mainSlides.length) % mainSlides.length;
            updateMainCarousel();
        }
        
        function goToMainSlide(index) {
            mainCurrentSlide = index;
            updateMainCarousel();
        }
        
        // Auto avanzar carrusel principal cada 5 segundos
        let mainAutoSlide = setInterval(nextMainSlide, 5000);
        
        // Pausar auto slide al hacer hover
        const mainCarousel = document.getElementById('mainCarousel');
        mainCarousel.addEventListener('mouseenter', () => clearInterval(mainAutoSlide));
        mainCarousel.addEventListener('mouseleave', () => {
            mainAutoSlide = setInterval(nextMainSlide, 5000);
        });
        
        // ========== CARRUSEL DE SERVICIOS ==========
        let serviceCurrentSlide = 0;
        const servicesTrack = document.getElementById('servicesTrack');
        const serviceSlides = document.querySelectorAll('.service-slide');
        const serviceDots = document.querySelectorAll('#serviceDots .carousel-dot');
        const slidesPerView = getSlidesPerView();
        
        function getSlidesPerView() {
            if (window.innerWidth < 769) return 1;
            if (window.innerWidth < 1025) return 2;
            return 3;
        }
        
        function updateServiceCarousel() {
            const slideWidth = serviceSlides[0].offsetWidth + 30; // ancho + gap
            const translateX = -serviceCurrentSlide * slideWidth * slidesPerView;
            servicesTrack.style.transform = `translateX(${translateX}px)`;
            
            // Actualizar puntos
            serviceDots.forEach((dot, index) => {
                dot.classList.toggle('active', index === serviceCurrentSlide);
            });
        }
        
        function nextServiceSlide() {
            const maxSlides = Math.ceil(serviceSlides.length / slidesPerView) - 1;
            if (serviceCurrentSlide < maxSlides) {
                serviceCurrentSlide++;
                updateServiceCarousel();
            }
        }
        
        function prevServiceSlide() {
            if (serviceCurrentSlide > 0) {
                serviceCurrentSlide--;
                updateServiceCarousel();
            }
        }
        
        function goToServiceSlide(index) {
            const maxSlides = Math.ceil(serviceSlides.length / slidesPerView) - 1;
            if (index >= 0 && index <= maxSlides) {
                serviceCurrentSlide = index;
                updateServiceCarousel();
            }
        }
        
        // Actualizar cuando cambia el tamaño de la ventana
        window.addEventListener('resize', () => {
            const newSlidesPerView = getSlidesPerView();
            if (newSlidesPerView !== slidesPerView) {
                updateServiceCarousel();
            }
        });
        
        // ========== FUNCIONES GENERALES ==========
        // Navegación suave
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if(targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if(targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Mostrar/ocultar botón volver arriba
        window.addEventListener('scroll', function() {
            const backToTop = document.querySelector('.back-to-top');
            if (window.scrollY > 300) {
                backToTop.style.display = 'flex';
            } else {
                backToTop.style.display = 'none';
            }
        });
        
        // Animaciones al hacer scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                }
            });
        }, observerOptions);
        
        // Observar elementos para animar
        document.querySelectorAll('.featured-box, .about-col, .service-slide, .testimonial-item').forEach(el => {
            observer.observe(el);
        });
        
        // Inicializar carruseles
        updateMainCarousel();
        updateServiceCarousel();
    </script>
</body>
</html>