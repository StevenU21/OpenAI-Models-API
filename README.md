# API de Integración OpenAI con Laravel 11

Esta API fue desarrollada en **Laravel 11** y utiliza el paquete **laravel open ai** para integrar los servicios principales de OpenAI, tales como:

- **Chatbot de Text (GPT-4o)**
- **Chat con Images (GPT-4o)**
- **Transcripción (Text-to-Speech: tts-1 / tts-1-hd)**  
- **Conversión de voz a texto (Speech-to-Text: Whisper)**  
- **Generación de imágenes (Text-to-Image con DALL·E 2 y DALL·E 3)**  
- **Traducción de textos**

La documentación se basa en la [documentación oficial de OpenAI](https://platform.openai.com/docs) y en las validaciones definidas en cada Request. Este README ofrece una descripción detallada de cada funcionalidad, los endpoints disponibles, las validaciones aplicadas y las instrucciones para la instalación y uso del proyecto.

---

## Índice

- [Instalación](#instalación)
- [Configuración](#configuración)
- [Endpoints](#endpoints)
  - [Chat](#chat)
  - [Traducción](#traducción)
  - [Text-to-Speech](#text-to-speech)
  - [Speech-to-Text](#speech-to-text)
  - [Text-to-Image](#text-to-image)
- [Validaciones y Reglas de Request](#validaciones-y-reglas-de-request)
- [Ejemplos de Uso](#ejemplos-de-uso)
- [Referencias](#referencias)

---

## Instalación

1. **Clonar el repositorio**

   ```bash
   git clone https://github.com/StevenU21/OpenAI-Models-API.git
   ```
    
    ```bash
   cd OpenAI-Models-API
   ```

2. **Instalar dependencias**

   Asegúrate de tener Composer instalado y ejecuta:

   ```bash
   composer install
   ```

3. **Configurar el entorno**

   Copia el archivo `.env.example` a `.env` y configura las variables de entorno (incluyendo las credenciales de OpenAI y la configuración de la base de datos):

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrar la base de datos (si aplica)**

   ```bash
   php artisan migrate
   ```

5. **Levantar el servidor**

   ```bash
   php artisan serve
   ```

---

## Configuración

- **OpenAI API Key:**  
  Configura la variable `OPENAI_API_KEY` en el archivo `.env` con tu clave de API de OpenAI.

- **Paquete laravel open ai:**  
  El paquete se encarga de gestionar las solicitudes hacia los servicios de OpenAI. Revisa la [documentación oficial del paquete](https://github.com/openai-php/laravel) para más detalles de configuración avanzada.

---

## Endpoints

La API se organiza en diferentes grupos de rutas. A continuación se detalla cada uno de los endpoints y su funcionalidad.

### Chat

Permite interactuar con el servicio de chatbot, obtener modelos disponibles, prompts y generar conversaciones.

- **GET `/api/chat/models`**  
  _Descripción:_ Retorna la lista de modelos disponibles para el chatbot.  
  _Nombre de la ruta:_ `chat.models`

- **GET `/api/chat/prompts`**  
  _Descripción:_ Obtiene una colección de prompts predefinidos.  
  _Nombre de la ruta:_ `chat.prompts`

- **POST `/api/chat`**  
  _Descripción:_ Envía un mensaje para iniciar una conversación.  
  _Nombre de la ruta:_ `chat.conversation`  
  _Validaciones:_  
  - `text`: requerido, cadena de texto (mín. 3, máx. 1000 caracteres)  
  - `model`: requerido, cadena  
  - `temperature`: requerido, numérico (entre 0 y 1.4)  
  - `prompt`: requerido, cadena

- **POST `/api/chat/streamed`**  
  _Descripción:_ Envía una solicitud de conversación con respuesta en tiempo real vía SSE.  
  _Nombre de la ruta:_ `chat.streamed.conversation`

- **POST `/api/chat/image`**  
  _Descripción:_ Genera imágenes basadas en una conversación o prompt.  
  _Nombre de la ruta:_ `chat.image.conversation`

### Traducción

Permite traducir textos entre diferentes idiomas.

- **GET `/api/translation/languages`**  
  _Descripción:_ Obtiene la lista de idiomas disponibles para traducción.  
  _Nombre de la ruta:_ `translation.languages`

- **POST `/api/translation`**  
  _Descripción:_ Traduce un texto del idioma de origen al de destino.  
  _Nombre de la ruta:_ `translation.translate`  
  _Validaciones:_  
  - `text`: requerido, cadena (máx. 1000 caracteres)  
  - `source_language`: requerido, cadena (máximo 2 caracteres, debe ser diferente al target)  
  - `target_language`: requerido, cadena (máximo 2 caracteres, debe ser diferente al source)

### Text-to-Speech

Convierte texto a audio utilizando modelos de TTS.

- **GET `/api/text-to-speech/models`**  
  _Descripción:_ Lista de modelos disponibles para Text-to-Speech.  
  _Nombre de la ruta:_ `text-to-speech.models`

- **GET `/api/text-to-speech/voices`**  
  _Descripción:_ Lista de voces disponibles.  
  _Nombre de la ruta:_ `text-to-speech.voices`

- **GET `/api/text-to-speech/voices/audio`**  
  _Descripción:_ Retorna ejemplos de audio de las voces disponibles.  
  _Nombre de la ruta:_ `text-to-speech.voices.audio`

- **GET `/api/text-to-speech/languages`**  
  _Descripción:_ Obtiene los idiomas soportados para la conversión.  
  _Nombre de la ruta:_ `text-to-speech.languages`

- **GET `/api/text-to-speech/response-formats`**  
  _Descripción:_ Lista de formatos de respuesta disponibles (mp3, opus, aac, flac, wav, pcm).  
  _Nombre de la ruta:_ `text-to-speech.response-formats`

- **POST `/api/text-to-speech`**  
  _Descripción:_ Convierte un texto en audio.  
  _Nombre de la ruta:_ `text-to-speech.text-to-speech`  
  _Validaciones:_  
  - `model`: requerido, cadena, valores permitidos: `tts-1, tts-1-hd`  
  - `input`: requerido, cadena (mín. 3, máx. 4096 caracteres)  
  - `voice`: requerido, cadena (valores permitidos: `alloy, ash, coral, echo, fable, onyx, nova, sage, shimmer`)  
  - `response_format`: cadena, valores permitidos: `mp3, opus, aac, flac, wav, pcm`  
  - `speed`: numérico (entre 0.25 y 4.0)  
  - `language`: cadena (debe pertenecer a la lista de idiomas soportados)

- **GET `/api/text-to-speech/generated-audio`**  
  _Descripción:_ Recupera los audios generados previamente.  
  _Nombre de la ruta:_ `text-to-speech.generate-audio`

- **POST `/api/text-to-speech/streamed`**  
  _Descripción:_ Convierte texto a audio con respuesta en streaming.  
  _Nombre de la ruta:_ `text-to-speech.streamed`

### Speech-to-Text

Convierte archivos de audio a texto.

- **GET `/api/speech-to-text/languages`**  
  _Descripción:_ Lista de idiomas disponibles para Speech-to-Text.  
  _Nombre de la ruta:_ `speech-to-text.languages`

- **GET `/api/speech-to-text/response-formats`**  
  _Descripción:_ Formatos de respuesta permitidos (json, text, srt, verbose_json, vtt).  
  _Nombre de la ruta:_ `speech-to-text.response-formats`

- **GET `/api/speech-to-text/timestamp-granularities`**  
  _Descripción:_ Opciones de granularidad para los timestamps (word, segment).  
  _Nombre de la ruta:_ `speech-to-text.timestamp-granularities`

- **GET `/api/speech-to-text/actions`**  
  _Descripción:_ Acciones soportadas para Speech-to-Text.  
  _Nombre de la ruta:_ `speech-to-text.actions`

- **POST `/api/speech-to-text`**  
  _Descripción:_ Convierte un archivo de audio a texto.  
  _Validaciones:_  
  - `file`: requerido, debe ser un archivo con extensión `mp3, mp4, mpeg, mpga, m4a, wav, webm` y máximo de 25MB.  
  - `language`: cadena, debe pertenecer a un listado de idiomas (por ejemplo: en, es, fr, etc.).  
  - `response_format`: cadena, valores permitidos: `json, text, srt, verbose_json, vtt`  
  - `temperature`: numérico (entre 0 y 1)  
  - `timestamp_granularities`: requerido si `response_format` es `verbose_json`, valores: `word, segment`

### Text-to-Image

Genera imágenes a partir de descripciones en texto utilizando los modelos de DALL·E.

- **GET `/api/text-to-image/models`**  
  _Descripción:_ Lista de modelos disponibles: `dall-e-2` y `dall-e-3`.  
  _Nombre de la ruta:_ `text-to-image.models`

- **GET `/api/text-to-image/quality`**  
  _Descripción:_ Opciones de calidad de imagen (por ejemplo, `standard` o `hd` según el modelo).  
  _Nombre de la ruta:_ `text-to-image.quality`

- **GET `/api/text-to-image/sizes`**  
  _Descripción:_ Tamaños permitidos para las imágenes.  
  _Nombre de la ruta:_ `text-to-image.sizes`

- **GET `/api/text-to-image/prompt`**  
  _Descripción:_ Tipos de prompt soportados para la generación de imágenes.  
  _Nombre de la ruta:_ `text-to-image.prompt`

- **GET `/api/text-to-image/response-formats`**  
  _Descripción:_ Formatos de respuesta (por ejemplo, URL o b64_json).  
  _Nombre de la ruta:_ `text-to-image.response-formats`

- **GET `/api/text-to-image/style`**  
  _Descripción:_ Estilos disponibles para la generación de imágenes (por ejemplo, realista, anime, cartoon, etc.).  
  _Nombre de la ruta:_ `text-to-image.style`

- **POST `/api/text-to-image`**  
  _Descripción:_ Genera una imagen a partir de un prompt.  
  _Validaciones:_  
  - `model`: requerido, valores permitidos: `dall-e-2, dall-e-3`  
  - `prompt`: requerido, cadena (mín. 8 caracteres). Además, se valida la longitud máxima:
    - Máximo 1000 caracteres para `dall-e-2`  
    - Máximo 4000 caracteres para `dall-e-3`
  - `type`: requerido, cadena, opciones: `realistic, anime, cartoon, futuristic, abstract, impressionist, pixel art, watercolor, noir, steampunk, fantasy, vintage, scifi, minimalist, hyperrealistic, dramatic`
  - `image_number`: entero, de 1 a 10. Se aplica:
    - Máximo 1 para `dall-e-3`  
    - Máximo 10 para `dall-e-2`
  - `style`: para `dall-e-3` es requerido y debe ser `vivid` o `natural`
  - `size`: requerido, con valores permitidos que dependen del modelo:
    - Para `dall-e-2`: `256x256, 512x512, 1024x1024`  
    - Para `dall-e-3`: `1024x1024, 1792x1024, 1024x1792`
  - `response_format`: cadena, valores permitidos: `url, b64_json`
  - `quality`: opcional, para:
    - `dall-e-2`: `standard`  
    - `dall-e-3`: `standard, hd`

---

## Validaciones y Reglas de Request

La API utiliza las validaciones propias de Laravel para asegurar que las solicitudes cumplen con los requisitos. A continuación se resumen algunas reglas clave:

### Chat Request

- **text:**  
  - Obligatorio  
  - Tipo: cadena  
  - Longitud mínima de 3 y máxima de 1000 caracteres

- **model:**  
  - Obligatorio  
  - Tipo: cadena

- **temperature:**  
  - Obligatorio  
  - Tipo: numérico, con valor entre 0 y 1.4

- **prompt:**  
  - Obligatorio  
  - Tipo: cadena

### Speech-to-Text Request

- **file:**  
  - Obligatorio  
  - Debe ser un archivo de audio con extensiones permitidas: `mp3, mp4, mpeg, mpga, m4a, wav, webm`  
  - Tamaño máximo: 25MB

- **language:**  
  - Opcional, pero debe pertenecer a la lista de códigos (ej.: en, es, fr, etc.)

- **response_format:**  
  - Opcional, valores permitidos: `json, text, srt, verbose_json, vtt`

- **temperature:**  
  - Opcional, numérico (entre 0 y 1)

- **timestamp_granularities:**  
  - Obligatorio si el `response_format` es `verbose_json`, valores: `word, segment`

### Text-to-Image Request

- **model:**  
  - Obligatorio  
  - Valores permitidos: `dall-e-2, dall-e-3`

- **prompt:**  
  - Obligatorio  
  - Tipo: cadena, mínimo 8 caracteres  
  - Longitud máxima variable según modelo:
    - `dall-e-2`: máximo 1000 caracteres  
    - `dall-e-3`: máximo 4000 caracteres

- **type:**  
  - Obligatorio  
  - Tipo: cadena con opciones predefinidas (ej.: realistic, anime, cartoon, etc.)

- **image_number:**  
  - Entero, entre 1 y 10  
  - Se limita a 1 para `dall-e-3` y hasta 10 para `dall-e-2`

- **style:**  
  - Para `dall-e-3` es obligatorio  
  - Valores permitidos: `vivid, natural`

- **size:**  
  - Obligatorio, con opciones que varían según el modelo

- **response_format:**  
  - Opcional, valores: `url, b64_json`

- **quality:**  
  - Opcional, valores dependen del modelo:
    - Para `dall-e-2`: `standard`  
    - Para `dall-e-3`: `standard, hd`

### Text-to-Speech Request

- **model:**  
  - Obligatorio  
  - Valores permitidos: `tts-1, tts-1-hd`

- **input:**  
  - Obligatorio  
  - Tipo: cadena, entre 3 y 4096 caracteres

- **voice:**  
  - Obligatorio  
  - Valores permitidos: `alloy, ash, coral, echo, fable, onyx, nova, sage, shimmer`

- **response_format:**  
  - Opcional, valores: `mp3, opus, aac, flac, wav, pcm`

- **speed:**  
  - Opcional, numérico (entre 0.25 y 4.0)

- **language:**  
  - Opcional, debe pertenecer a la lista de idiomas soportados

### Translation Request

- **text:**  
  - Obligatorio  
  - Tipo: cadena, máximo 1000 caracteres

- **source_language:**  
  - Obligatorio  
  - Tipo: cadena, máximo 2 caracteres y debe ser diferente de `target_language`

- **target_language:**  
  - Obligatorio  
  - Tipo: cadena, máximo 2 caracteres y diferente de `source_language`

---

## Ejemplos de Uso

### Ejemplo de Solicitud de Chat

```bash
curl -X POST http://localhost:8000/api/chat \
  -H "Content-Type: application/json" \
  -d '{
        "text": "Hola, ¿cómo estás?",
        "model": "gpt-4",
        "temperature": 0.7,
        "prompt": "Asistente de ayuda"
      }'
```

### Ejemplo de Solicitud de Traducción

```bash
curl -X POST http://localhost:8000/api/translation \
  -H "Content-Type: application/json" \
  -d '{
        "text": "Hello, how are you?",
        "source_language": "en",
        "target_language": "es"
      }'
```

### Ejemplo de Solicitud de Text-to-Speech

```bash
curl -X POST http://localhost:8000/api/text-to-speech \
  -H "Content-Type: application/json" \
  -d '{
        "model": "tts-1",
        "input": "Bienvenido a nuestro servicio",
        "voice": "nova",
        "response_format": "mp3",
        "speed": 1.0,
        "language": "es"
      }'
```

### Ejemplo de Solicitud de Speech-to-Text

```bash
curl -X POST http://localhost:8000/api/speech-to-text \
  -F "file=@/ruta/a/tu/audio.wav" \
  -F "language=es" \
  -F "response_format=json" \
  -F "temperature=0.5" \
  -F "timestamp_granularities=word"
```

### Ejemplo de Solicitud de Text-to-Image

```bash
curl -X POST http://localhost:8000/api/text-to-image \
  -H "Content-Type: application/json" \
  -d '{
        "model": "dall-e-3",
        "prompt": "Una ilustración futurista de una ciudad",
        "type": "futuristic",
        "image_number": 1,
        "style": "vivid",
        "size": "1024x1024",
        "response_format": "url"
      }'
```

---

## Referencias

- [Documentación oficial de OpenAI](https://platform.openai.com/docs)
- [Generación de Textos](https://platform.openai.com/docs/guides/text-generation)
- [Generación de Imágenes](https://platform.openai.com/docs/guides/images)
- [Generación de Audio a partir de Texto](https://platform.openai.com/docs/guides/text-to-speech)
- [Generación de Transcriptiones](https://platform.openai.com/docs/guides/speech-to-text)
- [Endpoints de la ApI de Open AI con Parámetros](https://platform.openai.com/docs/api-reference/chat)
- [Laravel Open AI (paquete)](https://github.com/laravel-open-ai)

---
