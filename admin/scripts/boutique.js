import { refreshNavbar } from "./navbar.js";
import { requestGET, requestPUT, requestDELETE, requestPATCH, requestPOST } from './ajax.js';
import { showLoader, hideLoader } from "./loader.js";
import { toast } from "./toaster.js";
import { showPropertieSkeleton, hidePropertieSkeleton } from "./propertieskeleton.js";
import { getFullFilepath, openFileDialog } from "./files.js";
import { getToggleStatus, updateToggleStatus } from "./toggle.js";

showPropertieSkeleton();

const prop_image = document.getElementById('prop_image');
const prop_name = document.getElementById('prop_name');
const prop_price = document.getElementById('prop_price');
const prop_xp = document.getElementById('prop_xp');
const prop_qte = document.getElementById('prop_qte');
const prop_reductions = document.getElementById('prop_reductions');
const save_btn = document.getElementById('save_btn');
const delete_btn = document.getElementById('delete_btn');
const new_btn = document.getElementById('new_btn');

async function fetchData() {
    let articles = [];
    try {
        articles = await requestGET('/item.php');
        console.log('articles =', articles);
    } catch (error) {
        console.error(error);
        toast('Erreur lors du chargement des articles.', true);
    }

    return articles.map(article => ({
        label: article.nom_article,
        id: article.id_article
    }));
}

async function saveArticle(id_article) {
    showLoader();

    const data = {
        name: prop_name.value,
        xp: prop_xp.value,
        stocks: prop_qte.value,
        price: prop_price.value,
        reduction: getToggleStatus(prop_reductions)
    };

    try {
        await requestPUT('/item.php?id=' + id_article.toString(), data);
        toast('Article mis à jour avec succès.');
        selectArticle(id_article);
    } catch (error) {
        toast(error.message, true);
    }

    hideLoader();
}

async function deleteArticle(id_article) {
    showLoader();

    try {
        await requestDELETE(`/item.php?id=${id_article}`);
        refreshNavbar(fetchData, selectArticle);
        toast('Article supprimé avec succès.');
    } catch (error) {
        toast(error.message, true);
    }

    hideLoader();
}

async function selectArticle(id_article, li) {
    showPropertieSkeleton();
    showLoader();

    try {
        const article = await requestGET(`/item.php?id=${id_article}`);

        prop_image.src = await getFullFilepath(
            article.image_article,
            '../ressources/default_images/boutique.png'
        );
        prop_name.value = article.nom_article;
        prop_xp.value = article.xp_article;
        prop_qte.value = article.stock_article;
        prop_price.value = article.prix_article;
        updateToggleStatus(prop_reductions, article.reduction_article);

        save_btn.onclick = () => {
            saveArticle(id_article);
        };

        delete_btn.onclick = () => {
            swal({
                title: "Êtes vous sûr ?",
                text: "Cette action est définitive",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    deleteArticle(id_article);
                }
            });
        };

        prop_name.onkeyup = () => {
            if (li) {
                li.textContent = prop_name.value;
            }
        };

        document.getElementById('prop_image_edit').onclick = async () => {
            const image = await openFileDialog();

            const url = URL.createObjectURL(image);
            prop_image.src = url;

            showLoader();

            try {
                await requestPATCH('/item.php?id=' + id_article.toString(), image);
                toast('Image mise à jour avec succès.');
            } catch (error) {
                toast(error.message, true);
            }

            hideLoader();
        };
    } catch (error) {
        console.error(error);
        toast(error.message, true);
    }

    hideLoader();
    hidePropertieSkeleton();
}

new_btn.onclick = async () => {
    showLoader();

    try {
        const created = await requestPOST('/item.php');
        const id = typeof created === 'object' ? created.id_article : created;
        refreshNavbar(fetchData, selectArticle, id);
    } catch (error) {
        console.error(error);
        toast(error.message || "Erreur lors de la création de l'article", true);
        hideLoader();
    }
};

refreshNavbar(fetchData, selectArticle);