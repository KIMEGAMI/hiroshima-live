export const SITE_URL = "https://hiroshima-live.shinji.work";
export const SITE_NAME = "hiroshima-live";
export const DEFAULT_IMAGE = `${SITE_URL}/favicon.png`;
export const DEFAULT_TITLE = "広島ライブ情報 | hiroshima-live";
export const DEFAULT_DESCRIPTION =
    "hiroshima-liveは、広島のライブ情報・ライブハウス情報を探せるライブ情報サイトです。";

export const setSeo = ({
    title = DEFAULT_TITLE,
    description = DEFAULT_DESCRIPTION,
    url = `${SITE_URL}/`,
    image = DEFAULT_IMAGE,
    robots = "index, follow",
    type = "website",
} = {}) => {
    document.title = title;

    setMeta("description", description);
    setMeta("robots", robots);

    setProperty("og:site_name", SITE_NAME);
    setProperty("og:type", type);
    setProperty("og:title", title);
    setProperty("og:description", description);
    setProperty("og:url", url);
    setProperty("og:image", image);
    setProperty("og:locale", "ja_JP");

    setMeta("twitter:card", "summary_large_image");
    setMeta("twitter:title", title);
    setMeta("twitter:description", description);
    setMeta("twitter:image", image);

    setCanonical(url);
};

const setMeta = (name, content) => {
    let tag = document.querySelector(`meta[name="${name}"]`);

    if (!tag) {
        tag = document.createElement("meta");
        tag.setAttribute("name", name);
        document.head.appendChild(tag);
    }

    tag.setAttribute("content", content);
};

const setProperty = (property, content) => {
    let tag = document.querySelector(`meta[property="${property}"]`);

    if (!tag) {
        tag = document.createElement("meta");
        tag.setAttribute("property", property);
        document.head.appendChild(tag);
    }

    tag.setAttribute("content", content);
};

const setCanonical = (href) => {
    let tag = document.querySelector('link[rel="canonical"]');

    if (!tag) {
        tag = document.createElement("link");
        tag.setAttribute("rel", "canonical");
        document.head.appendChild(tag);
    }

    tag.setAttribute("href", href);
};
