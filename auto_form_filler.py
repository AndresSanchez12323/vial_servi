#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Relleno Automático de Formularios - VialServi

Este script automatiza el llenado de formularios en el sistema VialServi.
Utiliza Selenium WebDriver para interactuar con los formularios web.

Formularios soportados:
- Registro de Cliente (registro_cliente.php)
- Formulario de Contacto (contactenos.php)
"""

import json
import time
import sys
import os
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException, NoSuchElementException
from faker import Faker
from dotenv import load_dotenv

# Cargar variables de entorno
load_dotenv()


class FormFiller:
    """Clase para automatizar el llenado de formularios web."""
    
    def __init__(self, base_url="http://localhost", headless=False):
        """
        Inicializa el rellenador de formularios.
        
        Args:
            base_url (str): URL base del sitio web
            headless (bool): Si True, ejecuta el navegador en modo headless
        """
        self.base_url = base_url
        self.fake = Faker('es_ES')  # Generador de datos falsos en español
        
        # Configurar opciones del navegador
        options = webdriver.ChromeOptions()
        if headless:
            options.add_argument('--headless')
        options.add_argument('--no-sandbox')
        options.add_argument('--disable-dev-shm-usage')
        
        try:
            self.driver = webdriver.Chrome(options=options)
            self.wait = WebDriverWait(self.driver, 10)
        except Exception as e:
            print(f"Error al inicializar el navegador: {e}")
            print("Asegúrate de tener Chrome y ChromeDriver instalados")
            sys.exit(1)
    
    def load_form_data(self, file_path="form_data.json"):
        """
        Carga datos de formularios desde un archivo JSON.
        
        Args:
            file_path (str): Ruta al archivo JSON con los datos
            
        Returns:
            dict: Datos de formularios
        """
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                return json.load(f)
        except FileNotFoundError:
            print(f"Archivo {file_path} no encontrado")
            return None
        except json.JSONDecodeError:
            print(f"Error al decodificar JSON en {file_path}")
            return None
    
    def fill_registro_cliente(self, data=None, use_fake_data=False):
        """
        Rellena el formulario de registro de cliente.
        
        Args:
            data (dict): Diccionario con los datos del formulario
            use_fake_data (bool): Si True, genera datos aleatorios
        """
        print("\n=== Rellenando Formulario de Registro de Cliente ===")
        
        # Navegar al formulario
        url = f"{self.base_url}/registro_cliente.php"
        self.driver.get(url)
        print(f"Navegando a: {url}")
        
        # Generar datos si es necesario
        if use_fake_data:
            password = self.fake.password(length=12)
            data = {
                'nombre': self.fake.first_name(),
                'apellido': self.fake.last_name(),
                'cedula': str(self.fake.random_int(min=10000000, max=99999999)),
                'telefono': f"300{self.fake.random_int(min=1000000, max=9999999)}",
                'email': self.fake.email(),
                'password': password,
                'confirm_password': password
            }
            print("Usando datos generados aleatoriamente")
        
        if not data:
            print("Error: No hay datos para rellenar el formulario")
            return False
        
        try:
            # Esperar a que el formulario esté cargado
            time.sleep(2)
            
            # Rellenar campos
            print("Rellenando campo 'nombre'...")
            nombre_field = self.driver.find_element(By.ID, "nombre")
            nombre_field.clear()
            nombre_field.send_keys(data['nombre'])
            time.sleep(0.5)
            
            print("Rellenando campo 'apellido'...")
            apellido_field = self.driver.find_element(By.ID, "apellido")
            apellido_field.clear()
            apellido_field.send_keys(data['apellido'])
            time.sleep(0.5)
            
            print("Rellenando campo 'cedula'...")
            cedula_field = self.driver.find_element(By.ID, "cedula")
            cedula_field.clear()
            cedula_field.send_keys(data['cedula'])
            time.sleep(0.5)
            
            print("Rellenando campo 'telefono'...")
            telefono_field = self.driver.find_element(By.ID, "telefono")
            telefono_field.clear()
            telefono_field.send_keys(data['telefono'])
            time.sleep(0.5)
            
            print("Rellenando campo 'email'...")
            email_field = self.driver.find_element(By.ID, "email")
            email_field.clear()
            email_field.send_keys(data['email'])
            time.sleep(0.5)
            
            print("Rellenando campo 'password'...")
            password_field = self.driver.find_element(By.ID, "password")
            password_field.clear()
            password_field.send_keys(data['password'])
            time.sleep(0.5)
            
            print("Rellenando campo 'confirm_password'...")
            confirm_password_field = self.driver.find_element(By.ID, "confirm_password")
            confirm_password_field.clear()
            confirm_password_field.send_keys(data['confirm_password'])
            time.sleep(0.5)
            
            print("\n✓ Formulario rellenado exitosamente")
            print("\nDatos ingresados:")
            for key, value in data.items():
                if 'password' in key:
                    print(f"  - {key}: ********")
                else:
                    print(f"  - {key}: {value}")
            
            # Preguntar si desea enviar el formulario
            print("\n¿Desea enviar el formulario? (s/n): ", end='')
            response = input().lower()
            
            if response == 's':
                submit_button = self.driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
                submit_button.click()
                print("Formulario enviado")
                time.sleep(3)
                return True
            else:
                print("Formulario NO enviado (solo prueba)")
                return True
                
        except NoSuchElementException as e:
            print(f"Error: No se pudo encontrar un elemento del formulario - {e}")
            return False
        except Exception as e:
            print(f"Error al rellenar el formulario: {e}")
            return False
    
    def fill_contactenos(self, data=None, use_fake_data=False):
        """
        Rellena el formulario de contacto.
        
        Args:
            data (dict): Diccionario con los datos del formulario
            use_fake_data (bool): Si True, genera datos aleatorios
        """
        print("\n=== Rellenando Formulario de Contacto ===")
        
        # Navegar al formulario
        url = f"{self.base_url}/contactenos.php"
        self.driver.get(url)
        print(f"Navegando a: {url}")
        
        # Generar datos si es necesario
        if use_fake_data:
            data = {
                'nombre': self.fake.name(),
                'email': self.fake.email(),
                'mensaje': self.fake.text(max_nb_chars=200)
            }
            print("Usando datos generados aleatoriamente")
        
        if not data:
            print("Error: No hay datos para rellenar el formulario")
            return False
        
        try:
            # Esperar a que el formulario esté cargado
            time.sleep(2)
            
            # Rellenar campos
            print("Rellenando campo 'nombre'...")
            nombre_field = self.driver.find_element(By.ID, "nombre")
            nombre_field.clear()
            nombre_field.send_keys(data['nombre'])
            time.sleep(0.5)
            
            print("Rellenando campo 'email'...")
            email_field = self.driver.find_element(By.ID, "email")
            email_field.clear()
            email_field.send_keys(data['email'])
            time.sleep(0.5)
            
            print("Rellenando campo 'mensaje'...")
            mensaje_field = self.driver.find_element(By.ID, "mensaje")
            mensaje_field.clear()
            mensaje_field.send_keys(data['mensaje'])
            time.sleep(0.5)
            
            print("\n✓ Formulario rellenado exitosamente")
            print("\nDatos ingresados:")
            for key, value in data.items():
                print(f"  - {key}: {value}")
            
            # Preguntar si desea enviar el formulario
            print("\n¿Desea enviar el formulario? (s/n): ", end='')
            response = input().lower()
            
            if response == 's':
                submit_button = self.driver.find_element(By.CSS_SELECTOR, "button[type='submit']")
                submit_button.click()
                print("Formulario enviado")
                time.sleep(3)
                return True
            else:
                print("Formulario NO enviado (solo prueba)")
                return True
                
        except NoSuchElementException as e:
            print(f"Error: No se pudo encontrar un elemento del formulario - {e}")
            return False
        except Exception as e:
            print(f"Error al rellenar el formulario: {e}")
            return False
    
    def close(self):
        """Cierra el navegador."""
        if self.driver:
            self.driver.quit()
            print("\nNavegador cerrado")


def print_menu():
    """Muestra el menú principal."""
    print("\n" + "="*60)
    print("  RELLENADOR AUTOMÁTICO DE FORMULARIOS - VialServi")
    print("="*60)
    print("\nOpciones disponibles:")
    print("  1. Rellenar formulario de Registro de Cliente")
    print("  2. Rellenar formulario de Contacto")
    print("  3. Rellenar todos los formularios")
    print("  4. Salir")
    print("\nModos:")
    print("  - Agregar 'f' al final para usar datos del archivo JSON")
    print("  - Por defecto se generan datos aleatorios")
    print("\nEjemplos: 1, 1f, 2, 2f, 3, 3f")
    print("-"*60)


def main():
    """Función principal."""
    print("\n" + "="*60)
    print("  Bienvenido al Rellenador Automático de Formularios")
    print("="*60)
    
    # Intentar cargar configuración desde .env
    base_url = os.getenv('BASE_URL', None)
    form_data_file = os.getenv('FORM_DATA_FILE', 'form_data.json')
    headless_mode = os.getenv('HEADLESS', 'false').lower() == 'true'
    
    # Si no hay .env, solicitar URL base
    if not base_url:
        print("\nIngrese la URL base del sitio web")
        print("(Presione Enter para usar 'http://localhost'): ", end='')
        base_url = input().strip()
        if not base_url:
            base_url = "http://localhost"
    
    print(f"\nUsando URL base: {base_url}")
    if headless_mode:
        print("Modo: Headless (sin ventana del navegador)")
    
    # Inicializar el rellenador
    filler = FormFiller(base_url=base_url, headless=headless_mode)
    
    # Cargar datos del archivo
    form_data = filler.load_form_data(form_data_file)
    
    try:
        while True:
            print_menu()
            print("\nIngrese su opción: ", end='')
            choice = input().strip().lower()
            
            # Determinar si usar archivo o datos falsos
            use_file = choice.endswith('f')
            if use_file:
                choice = choice[:-1]
            
            use_fake = not use_file
            
            if choice == '1':
                # Registro de cliente
                data = None
                if not use_fake and form_data:
                    data = form_data.get('registro_cliente')
                filler.fill_registro_cliente(data=data, use_fake_data=use_fake)
                
            elif choice == '2':
                # Contacto
                data = None
                if not use_fake and form_data:
                    data = form_data.get('contactenos')
                filler.fill_contactenos(data=data, use_fake_data=use_fake)
                
            elif choice == '3':
                # Todos los formularios
                print("\n=== Rellenando todos los formularios ===")
                
                # Registro de cliente
                data = None
                if not use_fake and form_data:
                    data = form_data.get('registro_cliente')
                filler.fill_registro_cliente(data=data, use_fake_data=use_fake)
                
                time.sleep(2)
                
                # Contacto
                data = None
                if not use_fake and form_data:
                    data = form_data.get('contactenos')
                filler.fill_contactenos(data=data, use_fake_data=use_fake)
                
            elif choice == '4':
                print("\nSaliendo...")
                break
                
            else:
                print("\nOpción inválida. Por favor intente de nuevo.")
            
            print("\n" + "-"*60)
            print("Presione Enter para continuar...")
            input()
    
    except KeyboardInterrupt:
        print("\n\nPrograma interrumpido por el usuario")
    
    finally:
        filler.close()


if __name__ == "__main__":
    main()
