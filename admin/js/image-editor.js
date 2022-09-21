/**
 * Image Editor JS
 */

console.log('fileEditorObj', fileEditorObj);

const { action, ajaxUrl } = fileEditorObj;

const { TABS, TOOLS } = FilerobotImageEditor;
const config = {
    source: 'https://scaleflex.airstore.io/demo/stephen-walker-unsplash.jpg',
    onSave: async (editedImageObject, designState) => {
        console.log('designState', designState);
        console.log('editedImageObject', editedImageObject);

        const formData = new FormData();
        formData.append('action', action);
        formData.append('example', 123);
        const res = await fetch(ajaxUrl, {
            method: 'POST',
            body: formData
        });
    },
    annotationsCommon: {
        fill: '#ff0000',
    },
    Text: { text: 'VTA Image Editor' },
    Rotate: { angle: 90, componentType: 'slider' },
    translations: {
        profile: 'Profile',
        coverPhoto: 'Cover photo',
        facebook: 'Facebook',
        socialMedia: 'Social Media',
        fbProfileSize: '180x180px',
        fbCoverPhotoSize: '820x312px',
    },
    Crop: {
        presetsItems: [
            {
                titleKey: 'classicTv',
                descriptionKey: '4:3',
                ratio: 4 / 3,
                // icon: CropClassicTv, // optional, CropClassicTv is a React Function component. Possible (React Function component, string or HTML Element)
            },
            {
                titleKey: 'cinemascope',
                descriptionKey: '21:9',
                ratio: 21 / 9,
                // icon: CropCinemaScope, // optional, CropCinemaScope is a React Function component.  Possible (React Function component, string or HTML Element)
            },
        ],
        // presetsFolders: [
        //     {
        //         titleKey: 'socialMedia', // will be translated into Social Media as backend contains this translation key
        //         // icon: Social, // optional, Social is a React Function component. Possible (React Function component, string or HTML Element)
        //         groups: [
        //             {
        //                 titleKey: 'facebook',
        //                 items: [
        //                     {
        //                         titleKey: 'profile',
        //                         width: 180,
        //                         height: 180,
        //                         descriptionKey: 'fbProfileSize',
        //                     },
        //                     {
        //                         titleKey: 'coverPhoto',
        //                         width: 820,
        //                         height: 312,
        //                         descriptionKey: 'fbCoverPhotoSize',
        //                     },
        //                 ],
        //             },
        //         ],
        //     },
        // ],
    },
    tabsIds: [TABS.ADJUST, TABS.ANNOTATE, TABS.WATERMARK], // or ['Adjust', 'Annotate', 'Watermark']
    defaultTabId: TABS.ANNOTATE, // or 'Annotate'
    defaultToolId: TOOLS.TEXT, // or 'Text'
};

console.log('TABS', TABS);
console.log('TOOLS', TOOLS);

// Assuming we have a div with id="editor_container"
const filerobotImageEditor = new FilerobotImageEditor(
    document.querySelector('#editor_container'),
    config,
);

filerobotImageEditor.render({
    onClose: (closingReason) => {
        console.log('Closing reason', closingReason);
        filerobotImageEditor.terminate();
    },
});
