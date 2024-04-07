
loadTime = (new Date()).getTime();

function uGen(old, a, q, r, m) {
var t;
t = Math.floor(old / q);
t = a * (old - (t * q)) - (t * r);
return Math.round((t < 0) ? (t + m) : t);
}

function LEnext() {
var i;
this.gen1 = uGen(this.gen1, 40014, 53668, 12211, 2147483563);
this.gen2 = uGen(this.gen2, 40692, 52774, 3791, 2147483399);
i = Math.floor(this.state / 67108862);
this.state = Math.round((this.shuffle[i] + this.gen2) % 2147483563);
this.shuffle[i] = this.gen1;
return this.state;
}

function LEnint(n) {
return Math.floor(this.next() / (1 + 2147483562 / (n + 1)));
}

function LEcuyer(s) {
var i;

this.shuffle = new Array(32);
this.gen1 = this.gen2 = (s & 0x7FFFFFFF);
for (i = 0; i < 19; i++) {
this.gen1 = uGen(this.gen1, 40014, 53668, 12211, 2147483563);
}

for (i = 0; i < 32; i++) {
this.gen1 = uGen(this.gen1, 40014, 53668, 12211, 2147483563);
this.shuffle[31 - i] = this.gen1;
}
this.state = this.shuffle[0];
this.next = LEnext;
this.nextInt = LEnint;
}

function sepchar() {
if (rsep) {
var seps = "!#$%&()*+,-./:;<=>?@[]^_{|}~";
return seps.charAt(sepran.nextInt(seps.length - 1));
}
return "-";
}

function gen() {
	str = ''
	while (! str.match(/\d/) ){
		str = generate()
	}
	return str
}

function generate() {
window.status = "Generating...";

rsep = false;
english = true;
gibberish = false;
clockseed = true;
makesig = false;
npass = 1;
pw_length = 7;
sep = 7;
	linelen = 7;
	numeric = false;
//               01234567890123456789012345678901
charcodes = " " +
"!\"#$%&'()*+,-./0123456789:;<=>?" +
"@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_" +
"`abcdefghijklmnopqrstuvwxyz{|}~";

var n, j, ran0;

seed = Math.round((new Date()).getTime() % Math.pow(2, 31));
ran0 = new LEcuyer((seed ^ Math.round(loadTime % Math.pow(2, 31))) & 0x7FFFFFFF);
for (j = 0; j < (5 + ((seed >> 3) & 0xF)); j++) {
n = ran0.nextInt(31);
}
while (n-- >= 0) {
seed = ((seed << 11) | (seed >>> (32 - 11))) ^ ran0.next();
}
seed &= 0x7FFFFFFF;

ran1 = new LEcuyer(seed);
if (rsep) {
sepran = new LEcuyer(seed);
}

ndig = 1;
j = 10;
while (npass >= j) {
ndig++;
j *= 10;
}
pw_item = pw_length + (sep > 0 ? (pw_length / sep) : 0);
pw_item += ndig + 5;
j = pw_item * 3;
if (j < 132) {
j = 132;
}
npline = Math.floor(linelen / pw_item);
if (npline < 1) {
npline = 0;
}
v = "";
md5v = "";
lineno = 0;
	letters = "abcdefghijklmnopqrstuvwxyz";
	
frequency = new Array( 
new Array(4, 20, 28, 52, 2, 11, 28, 4, 32, 4, 6, 62,
23, 167, 2, 14, 0, 83, 76, 127, 7, 25, 8, 1,
9, 1), /* aa - az */

new Array(13, 0, 0, 0, 55, 0, 0, 0, 8, 2, 0, 22, 0, 0,
11, 0, 0, 15, 4, 2, 13, 0, 0, 0, 15, 0), /* ba - bz */

new Array(32, 0, 7, 1, 69, 0, 0, 33, 17, 0, 10, 9, 1,
0, 50, 3, 0, 10, 0, 28, 11, 0, 0, 0, 3, 0), /* ca - cz */

new Array(40, 16, 9, 5, 65, 18, 3, 9, 56, 0, 1, 4, 15,
6, 16, 4, 0, 21, 18, 53, 19, 5, 15, 0, 3, 0), /* da - dz */

new Array(84, 20, 55, 125, 51, 40, 19, 16, 50, 1, 4,
55, 54, 146, 35, 37, 6, 191, 149, 65, 9, 26,
21, 12, 5, 0), /* ea - ez */

new Array(19, 3, 5, 1, 19, 21, 1, 3, 30, 2, 0, 11, 1,
0, 51, 0, 0, 26, 8, 47, 6, 3, 3, 0, 2, 0), /* fa - fz */

new Array(20, 4, 3, 2, 35, 1, 3, 15, 18, 0, 0, 5, 1,
4, 21, 1, 1, 20, 9, 21, 9, 0, 5, 0, 1, 0), /* ga - gz */

new Array(101, 1, 3, 0, 270, 5, 1, 6, 57, 0, 0, 0, 3,
2, 44, 1, 0, 3, 10, 18, 6, 0, 5, 0, 3, 0), /* ha - hz */

new Array(40, 7, 51, 23, 25, 9, 11, 3, 0, 0, 2, 38,
25, 202, 56, 12, 1, 46, 79, 117, 1, 22, 0,
4, 0, 3), /* ia - iz */

new Array(3, 0, 0, 0, 5, 0, 0, 0, 1, 0, 0, 0, 0, 0, 4,
0, 0, 0, 0, 0, 3, 0, 0, 0, 0, 0), /* ja - jz */

new Array(1, 0, 0, 0, 11, 0, 0, 0, 13, 0, 0, 0, 0, 2,
0, 0, 0, 0, 6, 2, 1, 0, 2, 0, 1, 0), /* ka - kz */

new Array(44, 2, 5, 12, 62, 7, 5, 2, 42, 1, 1, 53, 2,
2, 25, 1, 1, 2, 16, 23, 9, 0, 1, 0, 33, 0), /* la - lz */

new Array(52, 14, 1, 0, 64, 0, 0, 3, 37, 0, 0, 0, 7,
1, 17, 18, 1, 2, 12, 3, 8, 0, 1, 0, 2, 0), /* ma - mz */

new Array(42, 10, 47, 122, 63, 19, 106, 12, 30, 1, 6,
6, 9, 7, 54, 7, 1, 7, 44, 124, 6, 1, 15, 0,
12, 0), /* na - nz */

new Array(7, 12, 14, 17, 5, 95, 3, 5, 14, 0, 0, 19,
41, 134, 13, 23, 0, 91, 23, 42, 55, 16, 28,
0, 4, 1), /* oa - oz */

new Array(19, 1, 0, 0, 37, 0, 0, 4, 8, 0, 0, 15, 1, 0,
27, 9, 0, 33, 14, 7, 6, 0, 0, 0, 0, 0), /* pa - pz */

new Array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
0, 0, 0, 0, 0, 17, 0, 0, 0, 0, 0), /* qa - qz */

new Array(83, 8, 16, 23, 169, 4, 8, 8, 77, 1, 10, 5,
26, 16, 60, 4, 0, 24, 37, 55, 6, 11, 4, 0,
28, 0), /* ra - rz */

new Array(65, 9, 17, 9, 73, 13, 1, 47, 75, 3, 0, 7,
11, 12, 56, 17, 6, 9, 48, 116, 35, 1, 28, 0,
4, 0), /* sa - sz */

new Array(57, 22, 3, 1, 76, 5, 2, 330, 126, 1, 0, 14,
10, 6, 79, 7, 0, 49, 50, 56, 21, 2, 27, 0,
24, 0), /* ta - tz */

new Array(11, 5, 9, 6, 9, 1, 6, 0, 9, 0, 1, 19, 5, 31,
1, 15, 0, 47, 39, 31, 0, 3, 0, 0, 0, 0), /* ua - uz */

new Array(7, 0, 0, 0, 72, 0, 0, 0, 28, 0, 0, 0, 0, 0,
5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 0), /* va - vz */

new Array(36, 1, 1, 0, 38, 0, 0, 33, 36, 0, 0, 4, 1,
8, 15, 0, 0, 0, 4, 2, 0, 0, 1, 0, 0, 0), /* wa - wz */

new Array(1, 0, 2, 0, 0, 1, 0, 0, 3, 0, 0, 0, 0, 0, 1,
5, 0, 0, 0, 3, 0, 0, 1, 0, 0, 0), /* xa - xz */

new Array(14, 5, 4, 2, 7, 12, 12, 6, 10, 0, 0, 3, 7,
5, 17, 3, 0, 4, 16, 30, 0, 0, 5, 0, 0, 0), /* ya - yz */

new Array(1, 0, 0, 0, 4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0) /* za - zz */ );

row_sums = new Array(
796,   160,    284,    401,    1276,   262,    199,    539,    777,    
16,    39,     351,    243,    751,    662,    181,    17,     683,    
662,   968,    248,    115,    180,    17,     162,    5
);

start_freq = new Array(
1299,  425,    725,    271,    375,    470,    93,     223,    1009,
24,    20,     355,    379,    319,    823,    618,    21,     317,
962,   1991,   271,    104,    516,    6,      16,     14
);

total_sum = 11646;
		password = "";
position = ran1.nextInt(total_sum - 1);
for (row_position = 0, j = 0; position >= row_position;
row_position += start_freq[j], j++) {
continue;
}

password = letters.charAt(i = j - 1);
nch = 1;

	position = Math.random() * (pw_length -1) + 1
        a = Math.ceil( position )
	b = Math.ceil(Math.random() * 9)

for (nchars = pw_length - 1; nchars; --nchars) {
	position = ran1.nextInt(row_sums[i] - 1);
	for (row_position = 0, j = 0;
		position >= row_position;
		row_position += frequency[i][j], j++) {
	}

	if ((sep > 0) && ((nch % sep) == 0)) {
		password += sepchar();
	}
	nch++;
	password += letters.charAt(i = j - 1);
	if (nchars == a){
		password += b
	}
}
	
	window.status = "Done.";
	return password 
}
