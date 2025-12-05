<?php

use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
    public function testPasswordHashing()
    {
        $password = 'testPassword123';
        $hash = password_hash($password, PASSWORD_BCRYPT);
        
        $this->assertNotEquals($password, $hash);
        $this->assertTrue(password_verify($password, $hash));
    }
    
    public function testEmailValidation()
    {
        $validEmail = 'user@example.com';
        $invalidEmail = 'not-an-email';

        $this->assertNotFalse(
            filter_var($validEmail, FILTER_VALIDATE_EMAIL)
        );
        $this->assertFalse(
            filter_var($invalidEmail, FILTER_VALIDATE_EMAIL)
        );
    }

    public function testGetBrowserInfo()
    {
        // Test Chrome detection
        $chromeUA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';
        $browser = $this->getBrowserFromUserAgent($chromeUA);
        $this->assertEquals('Chrome', $browser);

        // Test Firefox detection
        $firefoxUA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0';
        $browser = $this->getBrowserFromUserAgent($firefoxUA);
        $this->assertEquals('Firefox', $browser);

        // Test Safari detection
        $safariUA = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15';
        $browser = $this->getBrowserFromUserAgent($safariUA);
        $this->assertEquals('Safari', $browser);
    }

    public function testGetOperatingSystem()
    {
        // Test Windows 10 detection
        $win10UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
        $os = $this->getOSFromUserAgent($win10UA);
        $this->assertEquals('Windows 10', $os);

        // Test Mac OS detection
        $macUA = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36';
        $os = $this->getOSFromUserAgent($macUA);
        $this->assertEquals('Mac OS X', $os);

        // Test Linux detection
        $linuxUA = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36';
        $os = $this->getOSFromUserAgent($linuxUA);
        $this->assertEquals('Linux', $os);
    }

    public function testGetDeviceType()
    {
        // Test mobile detection
        $mobileUA = 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15';
        $device = $this->getDeviceFromUserAgent($mobileUA);
        $this->assertEquals('Mobile', $device);

        // Test tablet detection
        $tabletUA = 'Mozilla/5.0 (iPad; CPU OS 14_6 like Mac OS X) AppleWebKit/605.1.15';
        $device = $this->getDeviceFromUserAgent($tabletUA);
        $this->assertEquals('Tablet', $device);

        // Test desktop detection
        $desktopUA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
        $device = $this->getDeviceFromUserAgent($desktopUA);
        $this->assertEquals('Desktop', $device);
    }

    // Helper methods that replicate the app's logic
    private function getBrowserFromUserAgent($user_agent)
    {
        $browser = 'Unknown Browser';
        // Chrome must be checked before Safari because Chrome UA contains "Safari"
        $browser_array = array(
            '/msie/i'      => 'Internet Explorer',
            '/edge/i'      => 'Edge',
            '/chrome/i'    => 'Chrome',
            '/firefox/i'   => 'Firefox',
            '/safari/i'    => 'Safari',
            '/opera/i'     => 'Opera',
        );

        foreach ($browser_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $browser = $value;
                break;
            }
        }
        return $browser;
    }

    private function getOSFromUserAgent($user_agent)
    {
        $os_platform = 'Unknown OS';
        $os_array = array(
            '/windows nt 10/i'      => 'Windows 10',
            '/windows nt 6.3/i'     => 'Windows 8.1',
            '/windows nt 6.2/i'     => 'Windows 8',
            '/windows nt 6.1/i'     => 'Windows 7',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/linux/i'              => 'Linux',
            '/iphone/i'             => 'iPhone',
            '/android/i'            => 'Android',
        );

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
                break;
            }
        }
        return $os_platform;
    }

    private function getDeviceFromUserAgent($user_agent)
    {
        $device = 'Desktop';
        if (preg_match('/mobile|android|kindle|silk|midp|phone|blackberry/i', $user_agent)) {
            $device = 'Mobile';
        } elseif (preg_match('/tablet|ipad|playbook/i', $user_agent)) {
            $device = 'Tablet';
        }
        return $device;
    }
}