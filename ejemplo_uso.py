#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Ejemplo Rápido - Rellenador Automático de Formularios

Este es un script de ejemplo que muestra cómo usar el rellenador
de formularios de manera programática.
"""

import sys
import os

# Agregar el directorio actual al path para importar auto_form_filler
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from auto_form_filler import FormFiller


def ejemplo_basico():
    """Ejemplo básico de uso con datos generados automáticamente."""
    print("\n=== EJEMPLO 1: Datos generados automáticamente ===\n")
    
    # Crear instancia del rellenador
    filler = FormFiller(base_url="http://localhost", headless=False)
    
    try:
        # Rellenar formulario de registro con datos aleatorios
        filler.fill_registro_cliente(use_fake_data=True)
        
        # Esperar un momento
        import time
        time.sleep(2)
        
        # Rellenar formulario de contacto con datos aleatorios
        filler.fill_contactenos(use_fake_data=True)
        
    finally:
        # Cerrar el navegador
        filler.close()


def ejemplo_con_datos_personalizados():
    """Ejemplo usando datos personalizados."""
    print("\n=== EJEMPLO 2: Datos personalizados ===\n")
    
    # Datos personalizados para registro
    datos_registro = {
        'nombre': 'Carlos',
        'apellido': 'Rodriguez',
        'cedula': '1098765432',
        'telefono': '3101234567',
        'email': 'carlos.rodriguez@test.com',
        'password': 'Password123!',
        'confirm_password': 'Password123!'
    }
    
    # Datos personalizados para contacto
    datos_contacto = {
        'nombre': 'Ana María López',
        'email': 'ana.lopez@test.com',
        'mensaje': 'Hola, necesito información sobre los servicios de grúa. Gracias.'
    }
    
    # Crear instancia del rellenador
    filler = FormFiller(base_url="http://localhost", headless=False)
    
    try:
        # Rellenar formulario de registro con datos personalizados
        filler.fill_registro_cliente(data=datos_registro)
        
        # Esperar un momento
        import time
        time.sleep(2)
        
        # Rellenar formulario de contacto con datos personalizados
        filler.fill_contactenos(data=datos_contacto)
        
    finally:
        # Cerrar el navegador
        filler.close()


def ejemplo_desde_archivo():
    """Ejemplo cargando datos desde archivo JSON."""
    print("\n=== EJEMPLO 3: Datos desde archivo JSON ===\n")
    
    # Crear instancia del rellenador
    filler = FormFiller(base_url="http://localhost", headless=False)
    
    try:
        # Cargar datos del archivo
        form_data = filler.load_form_data('form_data.json')
        
        if form_data:
            # Rellenar formulario de registro
            filler.fill_registro_cliente(data=form_data.get('registro_cliente'))
            
            # Esperar un momento
            import time
            time.sleep(2)
            
            # Rellenar formulario de contacto
            filler.fill_contactenos(data=form_data.get('contactenos'))
        else:
            print("No se pudo cargar el archivo de datos")
            
    finally:
        # Cerrar el navegador
        filler.close()


def main():
    """Función principal que muestra el menú de ejemplos."""
    print("\n" + "="*60)
    print("  EJEMPLOS DE USO - Rellenador Automático de Formularios")
    print("="*60)
    print("\nSelecciona un ejemplo para ejecutar:")
    print("  1. Ejemplo básico (datos aleatorios)")
    print("  2. Ejemplo con datos personalizados")
    print("  3. Ejemplo desde archivo JSON")
    print("  4. Salir")
    print("-"*60)
    print("\nOpción: ", end='')
    
    choice = input().strip()
    
    if choice == '1':
        ejemplo_basico()
    elif choice == '2':
        ejemplo_con_datos_personalizados()
    elif choice == '3':
        ejemplo_desde_archivo()
    elif choice == '4':
        print("\nSaliendo...")
    else:
        print("\nOpción inválida")


if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\n\nPrograma interrumpido por el usuario")
    except Exception as e:
        print(f"\nError: {e}")
        import traceback
        traceback.print_exc()
