<?php
define ('BOOL_DONOTOPTIMIZE_ACTIONS', false);
define ('IMPORT_MAINFILE', 
'import io.appium.java_client.android.AndroidDriver;
import io.appium.java_client.ios.IOSDriver;
import io.appium.java_client.remote.MobileCapabilityType;
import io.appium.java_client.remote.MobilePlatform;
import java.net.URL;
import java.io.File;
import org.openqa.selenium.*;
import com.thoughtworks.selenium.webdriven.WebDriverBackedSelenium;
import org.openqa.selenium.remote.DesiredCapabilities;
import java.io.IOException;
import junit.framework.*;');


define ('IMPORT_SCENARIOSFILE', 'import org.openqa.selenium.WebDriver;');


define ('IMPORT_COMPONENTSFILE', 
    'import org.openqa.selenium.WebDriver;
	import java.io.IOException;
	import java.io.File;');


define ('IMPORT_TECHFILE', 
'import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import com.thoughtworks.selenium.webdriven.WebDriverBackedSelenium;
import org.openqa.selenium.support.ui.*;
import java.util.Date;
import java.util.Hashtable;
import java.io.IOException;
import io.appium.java_client.android.AndroidDriver;
import org.openqa.selenium.interactions.Action;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.Keys;');

define ('IMPORT_TESTPLANFILE', 'import junit.framework.Test;
import junit.framework.TestResult;
import junit.framework.TestSuite;');
?>