<?php

namespace Tests\Selenium;

use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    protected $webDriver;
    protected $baseUrl = 'http://localhost:8000';

    public function setUp(): void
    {
        // Establecer la ubicación del ChromeDriver
        $chromeDriverPath = dirname(dirname(__DIR__)) . '/drivers/chromedriver.exe';
        putenv("WEBDRIVER_CHROME_DRIVER={$chromeDriverPath}");
        
        $options = new ChromeOptions();
        // Agregar opciones para ejecutar en modo headless (sin interfaz gráfica)
        $options->addArguments(['--headless', '--disable-gpu', '--no-sandbox']);
        
        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        
        $this->webDriver = ChromeDriver::start($capabilities);
    }

    public function testAdminLogin()
    {
        // Navegar a la página de login de admin
        $this->webDriver->get($this->baseUrl . '/admin_login.php');

        // Encontrar elementos del formulario
        $usernameInput = $this->webDriver->findElement(WebDriverBy::name('username'));
        $passwordInput = $this->webDriver->findElement(WebDriverBy::name('password'));
        $submitButton = $this->webDriver->findElement(WebDriverBy::cssSelector('input[type="submit"]'));

        // Llenar el formulario
        $usernameInput->sendKeys('admin');
        $passwordInput->sendKeys('123');
        $submitButton->click();

        // Tomar screenshot del resultado
        $this->webDriver->takeScreenshot('tests/selenium/reports/login-result.png');

        // Verificar que estamos en el dashboard
        $this->assertStringContainsString('admin/index.php', $this->webDriver->getCurrentURL());
    }

    public function tearDown(): void
    {
        if ($this->webDriver) {
            $this->webDriver->quit();
        }
    }
}