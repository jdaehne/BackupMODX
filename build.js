const fs = require('fs');
const path = require('path');
const {minify} = require('terser');
const sass = require('sass');
const postcss = require('postcss');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');

// Read package information dynamically from package.json
const packageJson = require('./package.json');
const version = packageJson.version;
const currentYear = new Date().getFullYear();
const startYear = parseInt(packageJson.startYear) || currentYear;
const yearRange = currentYear > startYear ? `${startYear}-${currentYear}` : `${startYear}`;
const dateStr = new Date().toISOString().split('T')[0];

const copyrightRegex = new RegExp(`Copyright ${startYear}(-\\d{4})? by`, 'g');
const copyrightReplace = `Copyright ${yearRange} by`;
const apiRegex = new RegExp(`&copy; ${startYear}(-\\d{4})?`, 'g');
const apiReplace = `&copy; ${yearRange}`;
const banner = `/*!\n * ${packageJson.fullname} - ${packageJson.description}\n * Version: ${packageJson.version}\n * Build date: ${dateStr}\n */\n`;

let versionParts = version.split('-');
let versionNumber = versionParts[0];
let versionRelease = versionParts[1] || 'pl';
let versionFull = versionNumber + '-' + versionRelease;

// Helper: Replace string/regex in a file
function replaceInFile(filePath, regex, replacement, message = 'file') {
    if (!fs.existsSync(filePath)) {
        console.warn(`⚠ File not found: ${filePath}`);
        return;
    }
    let content = String(fs.readFileSync(filePath, 'utf8'));
    content = content.replace(regex, replacement);
    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`✓ Updated ${message}: ${filePath}`);
}

// Helper: Ensure directory structure exists
function ensureDirExists(dirPath) {
    if (!fs.existsSync(dirPath)) {
        fs.mkdirSync(dirPath, {recursive: true});
    }
}

// Helper: Copy a file
function copyFile(src, dest) {
    const fullSource = path.resolve(__dirname, src);
    const fullTarget = path.resolve(__dirname, dest);
    if (!fs.existsSync(fullSource)) {
        console.warn(`⚠ Source file not found for copy: ${src}`);
        return;
    }
    const targetDir = path.dirname(fullTarget);
    ensureDirExists(targetDir)
    fs.copyFileSync(fullSource, fullTarget);
    console.log(`✓ Copied: ${src} -> ${dest}`);
}

// Helper: Copy folders recursively with an optional filter function
async function copyFolderRecursive(src, dest, filterFn = () => true) {
    if (!fs.existsSync(src)) {
        console.warn(`⚠ Source file not found for copy: ${src}`);
        return;
    }
    ensureDirExists(dest);
    const entries = fs.readdirSync(src, {withFileTypes: true});

    for (let entry of entries) {
        const srcPath = path.join(src, entry.name);
        const destPath = path.join(dest, entry.name);

        if (entry.isDirectory()) {
            await copyFolderRecursive(srcPath, destPath, filterFn);
        } else if (filterFn(entry.name, srcPath)) {
            fs.copyFileSync(srcPath, destPath);
            console.log(`✓ Copied: ${srcPath} -> ${destPath}`);
        }
    }
}

// Helper function to compile scripts
async function compileScripts(files, dest, filename) {
    console.log('Compiling scripts...');
    let combinedCode = files.map(f => fs.readFileSync(f, 'utf8')).join('\n');
    const minified = await minify(combinedCode, {
        mangle: true,
        compress: true
    });
    const finalCode = banner + minified.code;
    fs.mkdirSync(dest, {recursive: true});
    fs.writeFileSync(path.join(dest, filename), finalCode, 'utf8');
}

// Helper function to compile, autoprefix and minify Sass
async function compileSass(src, intermediate, dest, filename) {
    console.log('Compiling Sass & processing CSS...');
    const sassResult = sass.compile(src, {style: 'expanded'});
    ensureDirExists(intermediate);
    fs.writeFileSync(path.join(intermediate, filename), sassResult.css, 'utf8');
    const postcssResult = await postcss([
        autoprefixer(),
        cssnano({preset: ['default', {discardComments: {removeAll: true}}]})
    ]).process(sassResult.css, {from: undefined});
    const finalCss = postcssResult.css + '\n' + banner;
    ensureDirExists(dest);
    filename = filename.replace(/(\.\w+)$/i, '.min$1');

    fs.writeFileSync(path.join(dest, filename), finalCss, 'utf8');
}

async function taskBump() {
    console.log(`Bump (with version ${version} and daterange: ${yearRange})...`);
    const copyrightFiles = [
        'core/components/backupmodx/model/backupmodx/backupmodx.class.php',
        'core/components/backupmodx/src/BackupMODX.php',
    ];
    copyrightFiles.forEach(file => {
        replaceInFile(file, copyrightRegex, copyrightReplace, 'copyright in');
    });
    replaceInFile(
        'core/components/backupmodx/src/BackupMODX.php',
        /version = '\d+\.\d+\.\d+-?[0-9a-z]*'/ig,
        `version = '${version}'`,
        'version in'
    );
    replaceInFile(
        'source/js/mgr/backupmodx.js',
        apiRegex,
        apiReplace,
        'daterange in'
    );
    replaceInFile(
        '_build/config.json',
        /"version": "\d+\.\d+\.\d+-?[0-9a-z]*"/ig,
        `"version": "${version}"`,
        'version in'
    );
    replaceInFile(
        'core/components/backupmodx/composer.json',
        /"version": "\d+\.\d+\.\d+-?[0-9a-z]*"/ig,
        `"version": "${version}"`,
        'version in'
    );
}

async function taskCopy() {
    console.log('Copy files...');
    const copyFiles = [
        ['LICENSE.md', 'core/components/backupmodx/docs/license.md'],
        ['CHANGELOG.md', 'core/components/backupmodx/docs/changelog.md']
    ];
    copyFiles.forEach(([source, destination]) => {
        if (source && destination) {
            copyFile(source, destination);
        } else {
            console.warn('⚠ copyFiles: Invalid file pair detected.');
        }
    });
}

async function taskScripts() {
    await compileScripts([
        'source/js/mgr/backupmodx.js',
        'source/js/mgr/helper/util.js'
    ], 'assets/components/backupmodx/js/mgr/', 'backupmodx.min.js');
}

async function taskSass() {
    await compileSass(
        'source/sass/mgr/backupmodx.scss',
        'source/css/mgr/',
        'assets/components/backupmodx/css/mgr/',
        'backupmodx.css'
    );
}

async function taskImages() {
    console.log('Copying images...');
    const isImageFilter = (fileName) => /\.(png|jpg|gif|svg)$/i.test(fileName);
    await copyFolderRecursive('source/img', 'assets/components/backupmodx/img', isImageFilter);
}

const action = process.argv[2];
if (action === 'bump') {
    taskBump();
} else if (action === 'copy') {
    taskCopy();
} else if (action === 'scripts') {
    taskScripts();
} else if (action === 'sass') {
    taskSass();
} else if (action === 'images') {
    taskImages();
} else {
    // Default: Beides ausführen
    taskBump().then(() => taskScripts()).then(() => taskSass().then(() => taskCopy()).then(() => taskImages()));
}

console.log('Done!');
