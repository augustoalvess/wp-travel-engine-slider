=== WP Travel Engine Sliders ===
Contributors: Seu Nome
Tags: wp-travel-engine, slider, tours, travel, shortcode
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Sliders personalizados para viagens do WP Travel Engine com shortcodes configuráveis.

== Descrição ==

O WP Travel Engine Sliders adiciona dois shortcodes poderosos para exibir suas viagens do WP Travel Engine em formatos de slider atraentes e responsivos.

**Características Principais:**

* **Slider Destaque 1**: Exibe 1 viagem por slide em formato grande, ideal para hero sections
* **Slider Destaque 2**: Exibe 3 viagens por slide em formato de cards, perfeito para seções de destaque
* **Suporte a Vídeo**: Exibe vídeos do YouTube/Vimeo da galeria das viagens
* **Badge de Promoção**: Destaque automático para viagens com preço promocional
* **Totalmente Responsivo**: Adaptável a dispositivos móveis, tablets e desktop
* **Shortcodes Configuráveis**: Personalize tags, autoplay, velocidade e mais
* **Navegação Completa**: Setas de navegação, dots e suporte a teclado/touch
* **Vanilla JavaScript**: Sem dependências de bibliotecas externas
* **Template Override**: Permite personalizar templates no tema

== Instalação ==

1. Certifique-se de que o plugin **WP Travel Engine** está instalado e ativado
2. Faça upload da pasta `wp-travel-engine-sliders` para o diretório `/wp-content/plugins/`
3. Ative o plugin através do menu 'Plugins' no WordPress
4. Use os shortcodes em suas páginas ou posts

== Uso ==

**Shortcode Slider Destaque 1 (1 item por slide):**

```
[wte_slider_destaque_1]
```

**Parâmetros opcionais:**
* `tag` - Slug da tag para filtrar viagens (padrão: "destaque-1")
* `limit` - Número máximo de viagens (-1 para todas)
* `autoplay` - Ativar autoplay (true/false, padrão: true)
* `speed` - Velocidade do autoplay em milissegundos (padrão: 5000)
* `arrows` - Mostrar setas de navegação (true/false, padrão: true)

**Exemplo com parâmetros:**
```
[wte_slider_destaque_1 tag="promocoes" limit="5" autoplay="true" speed="3000"]
```

---

**Shortcode Slider Destaque 2 (3 itens por slide):**

```
[wte_slider_destaque_2]
```

**Parâmetros opcionais:**
* `tag` - Slug da tag para filtrar viagens (padrão: "destaque-2")
* `limit` - Número máximo de viagens (-1 para todas)
* `autoplay` - Ativar autoplay (true/false, padrão: false)
* `speed` - Velocidade do autoplay em milissegundos (padrão: 5000)
* `arrows` - Mostrar setas de navegação (true/false, padrão: true)
* `per_page` - Número de itens por slide (padrão: 3)

**Exemplo com parâmetros:**
```
[wte_slider_destaque_2 tag="especiais" per_page="3" autoplay="false"]
```

== Configuração das Viagens ==

**Para que as viagens apareçam nos sliders:**

1. Acesse **Viagens** no admin do WordPress
2. Edite ou crie uma viagem
3. No editor, adicione a tag correspondente:
   * Para Slider 1: adicione a tag `destaque-1`
   * Para Slider 2: adicione a tag `destaque-2`
4. Configure a imagem destaque (obrigatório)
5. Opcionalmente, adicione vídeo na galeria da viagem
6. Configure o preço regular e, se houver promoção, o preço promocional
7. Publique ou atualize a viagem

**Badge "Promoção":**
O badge aparece automaticamente quando a viagem tem um preço promocional (sale price) configurado e menor que o preço regular.

== Personalização ==

**Override de Templates:**

Você pode personalizar os templates copiando os arquivos do plugin para o seu tema:

Do plugin:
```
/wp-content/plugins/wp-travel-engine-sliders/templates/slider-destaque-1.php
```

Para o tema:
```
/wp-content/themes/seu-tema/wte-sliders/slider-destaque-1.php
```

**Arquivos de template disponíveis:**
* `slider-destaque-1.php` - Template do slider 1
* `slider-destaque-2.php` - Template do slider 2
* `partials/trip-card-large.php` - Card grande (slider 1)
* `partials/trip-card-small.php` - Card pequeno (slider 2)

**Personalização de CSS:**

Adicione CSS personalizado no seu tema para sobrescrever os estilos:

```css
/* Alterar cor primária */
.wte-trip-button {
    background: #ff6600;
}

/* Alterar cor do badge */
.wte-trip-badge {
    background: #e91e63;
}
```

== Perguntas Frequentes ==

= O plugin funciona sem o WP Travel Engine? =

Não. Este plugin é uma extensão do WP Travel Engine e requer que ele esteja instalado e ativado.

= Como adiciono vídeos nas viagens? =

Os vídeos devem ser adicionados através da galeria da viagem no WP Travel Engine. O plugin suporta URLs do YouTube e Vimeo.

= Posso usar os sliders em widgets? =

Sim! Basta adicionar o shortcode em um widget de texto ou HTML.

= Como alterar o número de itens por slide no Slider 2? =

Use o parâmetro `per_page` no shortcode:
```
[wte_slider_destaque_2 per_page="2"]
```

= O slider é responsivo? =

Sim! O slider se adapta automaticamente a diferentes tamanhos de tela:
* Desktop: 3 itens (slider 2)
* Tablet: 2 itens (slider 2)
* Mobile: 1 item

= Posso ter múltiplos sliders na mesma página? =

Sim! Você pode usar quantos shortcodes quiser na mesma página.

== Screenshots ==

1. Slider Destaque 1 - Exibição de 1 viagem por slide com vídeo
2. Slider Destaque 2 - Exibição de 3 viagens por slide em cards
3. Configuração de tags nas viagens
4. Exemplo de uso dos shortcodes

== Changelog ==

= 1.0.0 =
* Versão inicial
* Shortcode para slider de 1 item (wte_slider_destaque_1)
* Shortcode para slider de 3 itens (wte_slider_destaque_2)
* Suporte a vídeos do YouTube e Vimeo
* Badge automático de promoção
* Navegação com setas, dots, teclado e touch
* Slider vanilla JavaScript (sem dependências)
* Templates personalizáveis
* Totalmente responsivo

== Upgrade Notice ==

= 1.0.0 =
Versão inicial do plugin.

== Requisitos ==

* WordPress 5.0 ou superior
* PHP 7.4 ou superior
* WP Travel Engine (plugin ativo)

== Suporte ==

Para suporte, dúvidas ou sugestões, entre em contato através do email: contato@seusite.com

== Créditos ==

Desenvolvido para integração perfeita com o WP Travel Engine.


[wte_slider type="1" ids="331800"]
[wte_slider type="1" tags="destaque-1,destaque-2"]
[wte_slider type="2" ids="331797,331800,331802"]
[wte_slider type="1" tags="destaque-1"]