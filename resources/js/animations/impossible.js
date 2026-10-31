import {
    WebGLRenderer, Scene, OrthographicCamera, BoxGeometry, Mesh, MeshBasicMaterial,
    Group, Vector3, Quaternion, DoubleSide,
} from 'three';

/**
 * The impossible triangle as a real object: three straight beams that
 * don't meet (x, then y, then z). From almost anywhere it's plainly
 * broken; seen exactly along (1, 1, 1) the far end of the last beam lands
 * on the start of the first, and it closes into the logo — the flat mark
 * in the hero (svg.walk__mark), line for line. walk.js orbits the camera
 * into that one view with the scroll, then swaps in the flat mark.
 *
 * Orthographic, flat colour per face direction (no light), drawn on demand.
 */

// In the flat mark's own units (280 wide): bar width and beam length, so
// that the view along (1, 1, 1) projects onto it exactly
const BAR = 37.62;
const LEN = 305.3;
const SIN60 = Math.sqrt(2 / 3);   // a world unit along an axis, projected (0.8165)
const COS45 = Math.SQRT1_2;

// The one view: looking down (1, 1, 1), with (1, −1, 0) up so the last
// beam (z) runs level along the bottom
const VIEW  = new Vector3(1, 1, 1).normalize();
const UP    = new Vector3(1, -1, 0).normalize();
const RIGHT = new Vector3().crossVectors(UP, VIEW);

// The flat mark's hole centroid (140, 152.79), as a point in the world
const ANCHOR = RIGHT.clone().multiplyScalar(140 - SIN60 * LEN)
    .addScaledVector(UP, -(152.79 - COS45 * LEN));

const BEAMS = [
    [[0, 0, 0], [LEN, BAR, BAR]],                       // along x
    [[LEN - BAR, 0, 0], [LEN, LEN, BAR]],               // along y
    [[LEN - BAR, LEN - BAR, 0], [LEN, LEN, LEN]],       // along z — ends nearest the camera
];

/**
 * @param {HTMLCanvasElement} canvas
 * @param {{ x: string, y: string, z: string }} colors  the faces facing each axis
 */
export function createImpossible(canvas, colors) {
    const renderer = new WebGLRenderer({ canvas, alpha: true, antialias: true });
    renderer.setClearColor(0x000000, 0);
    const scene  = new Scene();
    const camera = new OrthographicCamera(-1, 1, 1, -1, -6000, 6000);

    const mat = (c) => new MeshBasicMaterial({ color: c, side: DoubleSide });   // the mirror flips the winding
    const [mx, my, mz] = [mat(colors.x), mat(colors.y), mat(colors.z)];
    const faces = [mx, mx, my, my, mz, mz];   // BoxGeometry's groups: ±x, ±y, ±z

    const body = new Group();
    for (const [[x0, y0, z0], [x1, y1, z1]] of BEAMS) {
        const beam = new Mesh(new BoxGeometry(x1 - x0, y1 - y0, z1 - z0), faces);
        beam.position.set((x0 + x1) / 2, (y0 + y1) / 2, (z0 + z1) / 2);
        body.add(beam);
    }
    const holder = new Group();
    holder.add(body);
    body.position.copy(ANCHOR).negate();      // the hole's centroid on the origin
    scene.add(holder);

    let vw = 1, vh = 1;
    const qa = new Quaternion(), qb = new Quaternion();
    const dir = new Vector3(), up = new Vector3(), side = new Vector3();

    return {
        resize(w, h) {
            vw = w;
            vh = h;
            renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
            renderer.setSize(w, h, false);
        },

        /**
         * x, y: where the hole's centroid sits on the canvas (px); k: px per
         * mark unit; yaw/pitch: the camera's turn away from the one view (rad)
         */
        render({ x, y, k, yaw = 0, pitch = 0 }) {
            // mirrored left to right: the beams as built close into the
            // logo's mirror image, so the view flips them back
            camera.left = x;
            camera.right = x - vw;
            camera.top = y;
            camera.bottom = y - vh;
            camera.updateProjectionMatrix();

            qa.setFromAxisAngle(UP, yaw);
            side.copy(RIGHT).applyQuaternion(qa);
            qb.setFromAxisAngle(side, pitch).multiply(qa);
            dir.copy(VIEW).applyQuaternion(qb);
            up.copy(UP).applyQuaternion(qb);

            holder.scale.setScalar(k);
            camera.position.copy(dir).multiplyScalar(3000);
            camera.up.copy(up);
            camera.lookAt(0, 0, 0);
            renderer.render(scene, camera);
        },
    };
}
